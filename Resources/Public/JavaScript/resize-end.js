/*!
 * BetterResizeEvent
 *
 * Author: Steffen Kroggel <developer@steffenkroggel.de>
 * Last updated: 22.06.2025
 * v2.0.1 – Renamed from ResizeEnd, internal scrolling state, added delta width+height detection
 *
 * This is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * Usage:
 * document.addEventListener('madj2k-better-resize-event', () => {
 *  console.log('BetterResizeEvent fired');
 * });
 *
 * Init:
 * document.addEventListener('DOMContentLoaded', () => {
 *  const resizeEvent = new BetterResizeEvent(); // Initialisiert bei DOM ready
 * });
 *
 * Notes:
 * - On iOS (Safari), viewport height and width can change during scroll bounce or keyboard animation.
 * - This version adds "viewport delta" detection to prevent false-positive resize events.
 * - Scrolling state is now internal (no more data-resizeend-scrolling attribute).
 * - New event: 'madj2k-better-resize-event' (legacy event still supported).
 */

class BetterResizeEvent {

  config = {
    scrollingEndTimeout: 500,
    resizeEndTimeout: 200,
    viewportDeltaThreshold: 50 // px threshold to filter small height/width changes (default: 50)
  };

  finalEventTimers = {};
  lastViewportHeight = window.innerHeight;
  lastViewportWidth = window.innerWidth;
  isScrolling = false;

  /**
   * Constructor
   * @param {Object} config - Optional config overrides
   */
  constructor(config = {}) {
    this.config = { ...this.config, ...config };

    this.lastViewportHeight = window.innerHeight;
    this.lastViewportWidth = window.innerWidth;
    this.isScrolling = false;

    this.initScrollingDetection();
    this.initResizeEvent();
  }

  /**
   * Init resize event
   */
  initResizeEvent() {
    window.addEventListener('resize', () => {
      const currentHeight = window.innerHeight;
      const currentWidth = window.innerWidth;

      const deltaH = Math.abs(this.lastViewportHeight - currentHeight);
      const deltaW = Math.abs(this.lastViewportWidth - currentWidth);

      // Skip if height/width change is below threshold (keyboard open/close or bounce)
      if (deltaH < this.config.viewportDeltaThreshold && deltaW < this.config.viewportDeltaThreshold) {
        return;
      }

      this.lastViewportHeight = currentHeight;
      this.lastViewportWidth = currentWidth;

      if (!this.isScrolling) {
        this.waitForFinalEvent(() => {
          // Skip if input is focused (e.g. keyboard open on mobile)
          const active = document.activeElement;
          if (!(active && active.tagName === 'INPUT')) {

            // New event
            const newEvent = new CustomEvent('madj2k-better-resize-event');
            document.dispatchEvent(newEvent);

            // Legacy event for backwards compatibility
            const legacyEvent = new CustomEvent('madj2k-resize-end');
            document.dispatchEvent(legacyEvent);
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
      this.isScrolling = true;

      this.waitForFinalEvent(() => {
        this.isScrolling = false;
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

/**
 * Legacy Wrapper: ResizeEnd
 *
 * Kept for backwards compatibility — will internally use BetterResizeEvent
 * Usage:
 * const resizeEnd = new ResizeEnd(); // will use BetterResizeEvent internally
 */
class ResizeEnd extends BetterResizeEvent {}
