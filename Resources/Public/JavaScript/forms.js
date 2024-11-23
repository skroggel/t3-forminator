/*!
 * Author: Steffen Kroggel <developer@steffenkroggel.de>
 * Last updated: 16.11.2024
 * v1.0.0
 *
 * This is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

/*
 * Example for initialization:
 *
 * $(() => {
 *  const forminator = new Forminator();
 *  forminator.initSelect2()
 *  forminator.initSelect2Resize();
 * });
 */
class Forminator {

  config = {
    'multiSelectClass': 'js-forminator-select2-multiple',
    'singleSelectClass': 'js-forminator-select2-single',
    'language': $('html').attr('lang') ?? 'en',
    'dir': 'ltr',
    'submitOnChangeClass' : 'js-forminator-submit-on-change',
    'reloadOnChangeClass' : 'js-forminator-reload-on-change',
    'skipValidationClass' : 'js-forminator-skip-validation'

  };

  resizeEnd = null;

  /**
   * Constructor
   * @param config
   */
  constructor(config) {
    this.config = {...this.config, ...config}
    this.resizeEnd = new ResizeEnd();

    this.initSubmitOnChange();
    this.initReloadOnChange();

  }

  /**
   * Init select2 fields
   */
  initSelect2() {
    $('.' + this.config.multiSelectClass).select2({
      closeOnSelect: false,
      allowClear: true,
      theme: 'multiple-dropdown',
      dir: this.config.dir,
      language: this.config.language,
    });

    $('.' + this.config.singleSelectClass).select2({
      allowClear: true,
      dir: this.config.dir,
      language: this.config.language
    });

    // add a special class to the default value
    $('.' + this.config.singleSelectClass).on('select2:select', function (e) {
      let data = e.params.data;
      let $renderedSpan = $(this).next('.select2-container').find('.select2-selection__rendered').first();
      if (! data.id) {
        $renderedSpan.addClass('select2-default-value');
      } else {
        $renderedSpan.remove('select2-default-value');
      }
    });
  }

  /**
   * Resize select2 according to new screen size
   */
  initSelect2Resize() {

    let self = this;
    $(document).on('madj2k-resize-end', function (e) {
      $('.' + self.config.multiSelectClass).select2('destroy');
      $('.' + self.config.singleSelectClass).select2('destroy');
      self.initSelect2();
    });
  }

  /**
   * Submit form on change of field
   */
  initSubmitOnChange () {

    $('.' + this.config.submitOnChangeClass).on('change', function (e) {
      let element = $(e.target);
      let form = element.closest('form');

      if (form.length) {
        form.submit();
      }
    });

  }


  /**
   * Reload form on change of field
   */
  initReloadOnChange () {

    let self = this;
    $('.' + this.config.reloadOnChangeClass).on('change', function (e) {
      let element = $(e.target);
      let form = element.closest('form');

      if (form.length) {
        form.find( self.config.reloadOnChangeClass).val(1);
        form.submit();
      }

    });

  }
}
