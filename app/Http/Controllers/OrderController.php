<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;
use Auth;
use Validator;
use App\Achievement_User;
use App\Order;
use App\Book_Order;
use App\Classes\ShortIdGenerator;
use Carbon\Carbon;
use Mail;

class OrderController extends Controller
{
    function checkout(Request $request, ShortIdGenerator $shortIdGenerator) {
        $user = Auth::user();
        $price = 0;

        $validator = Validator::make($request->all(), [
            'books' => self::REQUIRED . '|array',
        ]);

        if ($validator->fails()) {
            return self::RespondValidationError($request, $validator);
        }


        foreach ($request->books as $book) {
            $book_model = Book::where('user_id', $user->id)->find($book['id']);

            if (!$book_model) {
                return self::RespondModelNotFound();
            }

            $price_per_book = ($book_model->is_flip_over) ? self::FLIPOVER_PRICE : self::BOOK_PRICE;
            $price += $price_per_book * $book['amount'];
        }

        $achievements_points = $user->achievements()->sum('points');
        $can_get_free_shipping = ($achievements_points - $user->achievement_points_used >= 100) ? true : false;

        if (!$can_get_free_shipping) {
            $price += self::SHIPPING_PRICE;
        }
        else {
            $user->achievement_points_used += 100;
            $user->save();
        }

        $order = new Order();
        do {
            $shortId = $shortIdGenerator->generateId(8);
        } while ( count( Order::where('short_id', $shortId)->first()) >= 1 );
        $order->short_id = $shortId;
        $order->user_id = $user->id;
        $order->price = $price;
        $order->free_shipping = $can_get_free_shipping;
        $order->save();

        foreach ($request->books as $book) {
            $new_book_order = new Book_Order();
            $new_book_order->book_id = $book['id'];
            $new_book_order->order_id = $order->id;
            $new_book_order->save();

            $book = Book::find($book['id']);

            foreach ($book->posts()->get() as $post) {
                $post->is_printed = true;
                $post->save();
            }
        }

        self::sendOrderConfirmationEmail($order, $user);

        return response()->json([
            self::SUCCESS => true,
            'price' => $price,
            'order' => $order,
            self::ACHIEVEMENT => self::checkAchievementProgress(self::ORDER_BOOK)
        ]);
    }

    private function sendOrderConfirmationEmail($order, $user) {
        $today = Carbon::now();
        $estimated_arrival = $today->addDays(3);
        while ($estimated_arrival->isWeekend()) {
            $estimated_arrival->addDays(1);
        }

        $book_order = $order->books()->withPivot('amount')->get();

        Mail::send('emails.order-confirmation', [
            'order' => $order,
            'user' => $user,
            'estimated_arrival' => $estimated_arrival->format('jS \o\f F Y'),
            'book_order' => $book_order
        ], function($message) use($user){
            $message->to($user->email, 'Scribblr')
                    ->subject('Thanks for your purchase at Scribblr, ' . $user->first_name . '!')
                    ->from("info@scribblr.be", "Scribblr");
            //FIXME email of user
        });
    }

    function getPrices() {
        $user = Auth::user();

        $achievements_points = $user->achievements()->sum('points');
        $can_get_free_shipping = ($achievements_points - $user->achievement_points_used >= 100) ? true : false;
        $remaining_points = ($achievements_points - $user->achievement_points_used < 0) ? 0 : $achievements_points - $user->achievement_points_used;

        return response()->json([
            'book' => self::BOOK_PRICE,
            'flip_over' => self::FLIPOVER_PRICE,
            'shipping' => self::SHIPPING_PRICE,
            'can_get_free_shipping' => $can_get_free_shipping,
            'remaining_points' => $remaining_points
        ]);
    }
}
