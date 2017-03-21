import { ScribblrClientNg2Page } from './app.po';

describe('scribblr-client-ng2 App', () => {
  let page: ScribblrClientNg2Page;

  beforeEach(() => {
    page = new ScribblrClientNg2Page();
  });

  it('should display message saying app works', () => {
    page.navigateTo();
    expect(page.getParagraphText()).toEqual('app works!');
  });
});
