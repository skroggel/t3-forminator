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
 *  const resizeEnd = new ResizeEnd();
 * });
 */
class ResizeEnd {

  config = {
    scrollingEndTimeout: 500,
    resizeEndTimeout: 200,
    scrollingDataAttr: 'data-resizeend-scrolling'
  };

  finalEventTimers = {};

  /**
   * Constructor
   * @param config
   */
  constructor(config) {
    this.config = {...this.config, ...config}
    this.initScrollingDetection();
    this.initResizeEndEvent();
  }

  /**
   * Init resizeEnd-Event
   */
  initResizeEndEvent() {

    let self = this;
    $(window).resize(function () {

      // needed because if a mobile browser hides/shows the address-bar during scrolling this fires a resize event!
      if ($('body').attr(self.config.scrollingDataAttr) == 0) {
        self.waitForFinalEvent(
          function () {
            // do not fire event if keyboard is displayed on mobile!
            if ($(document.activeElement).is('input') === false) {
              $(document).trigger('madj2k-resize-end');
            }
          },
          self.config.resizeEndTimeout,
          'resize'
        );
      }
    });
  }

  /**
   * Check if scrolling is in place.
   * This is to prevent the resizeEvent on mobile when the address-bar hides/shows automatically
   */
  initScrollingDetection() {

    // touchmove is fired on iPad when the scrolling starts, scroll is fired when scrolling ends
    let self = this;
    $(window).on('scroll touchmove', function () {
      $('body').attr(self.config.scrollingDataAttr, 1);

      self.waitForFinalEvent(
        function () {
          $('body').attr(self.config.scrollingDataAttr, 0);
        },
        self.config.scrollingEndTimeout,
        "scrolling"
      );
    });
  }

  waitForFinalEvent (callback, ms, uniqueId) {
    if (!uniqueId) {
      uniqueId = "Don't call this twice without a uniqueId";
    }
    if (this.finalEventTimers[uniqueId]) {
      clearTimeout(this.finalEventTimers[uniqueId]);
    }
    this.finalEventTimers[uniqueId] = setTimeout(callback, ms);
  }
}
