/**
 * @version 1.0.0
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */

document.addEventListener("DOMContentLoaded", () => {
  const form = new Forms();
});

class Forms {
  constructor() {
    this.initReloadOnChange();
  }

  initReloadOnChange() {
    document.addEventListener('change', (event) => {
      if (event.target.matches('form .js-forminator-reload-on-change')) {
        const form = event.target.closest('form');
        const skipValidationInput = form.querySelector('.js-forminator-skip-validation');
        const submitButton = form.querySelector('button[name*="__currentPage"]'); // other variant: data-button-next

        if (skipValidationInput) {
          skipValidationInput.value = 1;
        }

        if (submitButton) {
          submitButton.click();
        }
      }
    });
  }
}

export default Forms;
