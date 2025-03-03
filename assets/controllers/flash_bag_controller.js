import { Controller } from '@hotwired/stimulus';
import { getComponent } from "@symfony/ux-live-component";

export default class extends Controller {
  async connect() {
    const flashBagContainer = this.element.closest("[data-controller='live']");
    const component = await this.getFlashBagComponent(flashBagContainer);

    setTimeout(async () => {
      await component.action('removeMessage', {id: this.element.id});
      this.element.remove();
    }, 3000);
  }

  async getFlashBagComponent(flashBagContainer) {
    return await getComponent(flashBagContainer);
  }
}
