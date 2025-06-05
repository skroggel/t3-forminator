/*!
 * Author: Steffen Kroggel <developer@steffenkroggel.de>
 * Last updated: 03.06.2025
 * v1.1.0 – Vanilla JS Port
 *
 * This is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * Usage:
 * document.addEventListener('madj2k-resize-end', () => {
 *  console.log('Resize-End fired');
 * });
 *
 * Init:
 * document.addEventListener('DOMContentLoaded', () => {
 *  const resizeEnd = new ResizeEnd(); // Initialisiert bei DOM ready
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
   * @param {Object} config - Optional config overrides
   */
  constructor(config = {}) {
    this.config = { ...this.config, ...config };
    this.initScrollingDetection();
    this.initResizeEndEvent();
  }

  /**
   * Init resizeEnd custom event
   */
  initResizeEndEvent() {
    window.addEventListener('resize', () => {
      const body = document.body;

      if (
        !body.hasAttribute(this.config.scrollingDataAttr) ||
        body.getAttribute(this.config.scrollingDataAttr) === '0'
      ) {
        this.waitForFinalEvent(() => {
          // Skip if input is focused (e.g. keyboard open on mobile)
          const active = document.activeElement;
          if (!(active && active.tagName === 'INPUT')) {
            const event = new CustomEvent('madj2k-resize-end');
            document.dispatchEvent(event);
          }
        }, this.config.resizeEndTimeout, 'resize');
      }
    });
  }

  /**
   * Init scrolling detection
   */
  initScrollingDetection() {
    const handler = () => {
      document.body.setAttribute(this.config.scrollingDataAttr, '1');

      this.waitForFinalEvent(() => {
        document.body.setAttribute(this.config.scrollingDataAttr, '0');
      }, this.config.scrollingEndTimeout, 'scrolling');
    };

    window.addEventListener('scroll', handler, { passive: true });
    window.addEventListener('touchmove', handler, { passive: true });
  }

  /**
   * Debounced final event dispatcher
   */
  waitForFinalEvent(callback, ms, uniqueId = "default") {
    if (this.finalEventTimers[uniqueId]) {
      clearTimeout(this.finalEventTimers[uniqueId]);
    }
    this.finalEventTimers[uniqueId] = setTimeout(callback, ms);
  }
}
