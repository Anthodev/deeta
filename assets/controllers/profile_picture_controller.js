import { Controller } from "@hotwired/stimulus";
import { getComponent } from "@symfony/ux-live-component";

export default class extends Controller {
  static targets = ["deleteButton"];

  async connect() {
    console.log("Profile picture controller connected");
    const componentElement = this.element.closest("[data-controller='live']");

    if (componentElement) {
      try {
        this.component = await getComponent(componentElement);
      } catch (error) {
        console.error("Error getting the component:", error);
      }
    } else {
      console.error("No Live Component found in the parent elements");
    }
  }

  async delete(event) {
    event.preventDefault();

    if (!this.component) {
      console.error("Le composant n'a pas été initialisé correctement");
      return;
    }

    if (confirm("Êtes-vous sûr de vouloir supprimer cette image de profil ?")) {
      try {
        await this.component.action("delete");

        window.location.reload();
      } catch (error) {
        console.error("Erreur lors de la suppression de l'image", error);
      }
    }
  }
}
