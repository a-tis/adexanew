/**
 *  Amasty Banner Slider widget
 *
 *  @copyright 2009-2020 Amasty Ltd
 *  @license   https://amasty.com/license.html
 */

define([
    'jquery',
    'underscore',
    'matchMedia',
    'mage/translate',
    'amSliderSplitEffect',
    'amSliderBubbleEffect',
    'amSlickSlider'
], function ($, _, mediaCheck, $t, amSliderSplitEffect, amSliderBubbleEffect) {
    'use strict';

    $.widget('am.bannerSlider', {
        options: {
            mediaBreakpoint: '(min-width: 768px)',
            sliderBlockWidth: null,
            sliderAnimationEffect: null,
            transitionSpeed: null,
            isAutoplay: false,
            sliderOptions: {},
            classes: {
                loaded: '-ambanner-loaded',
                hover: '-ambanner-hover',
                active: '-ambanner-active',
            },
            selectors: {
                showMoreButton: '[data-ambanner-js="show-more"]',
                sliderItem: '[data-ambanner-js="item"]',
                sliderWrapper: '[data-ambanner-js="slider-wrapper"]',
                overlayBlock: '[data-ambanner-js="overlay"]'
            },
            baseSliderOptions: {
                slidesToShow: 1,
                slidesToScroll: 1,
                adaptiveHeight: true,
                infinite: true,
                dotsClass: 'ambanner-slider-dots',
                nextArrow: '<button class="ambanner-arrow-button -next" title="' + $t('Next') + '">'
                    + $t('Next') + '</button>',
                prevArrow: '<button class="ambanner-arrow-button -prev" title="' + $t('Previous') + '">'
                    + $t('Previous') + '</button>'
            },
            animationEffects: {
                rolling: 0,
                split: 1,
                bubble: 2
            }
        },

        /**
         * @private
         * @returns {void}
         */
        _create: function () {
            switch (this.options.sliderAnimationEffect) {
                case this.options.animationEffects.rolling: default:
                    this.initSlider(this.element);
                    this._initListeners();
                    break;
                case this.options.animationEffects.split:
                    amSliderSplitEffect(this, this.element, this.options);
                    break;
                case this.options.animationEffects.bubble:
                    amSliderBubbleEffect(this, this.element, this.options);
                    this._initListeners();
                    break;
            }
        },

        /**
         * @private
         * @returns {void}
         */
        _initListeners: function () {
            var self = this,
                overlayBlock = $(self.options.selectors.overlayBlock, self.element);

            mediaCheck({
                media: self.options.mediaBreakpoint,
                entry: function () {
                    self.element.on('mouseenter.amBanner', function () {
                        self.hoverEffect(overlayBlock, self.element, true);
                    });

                    self.element.on('mouseleave.amBanner', function () {
                        self.hoverEffect(overlayBlock, self.element, false);
                    });

                    self.clearHover(self.element);
                },
                exit: function () {
                    self.showMoreEvent(self.element);

                    self.element
                        .off('mouseenter.amBanner mouseleave.amBanner')
                        .on('breakpoint', function(){
                        self.showMoreEvent(self.element);
                    });
                }
            });
        },

        /**
         * @param {Object} target - target overlayCircle block
         * @param {Object} hoverElement - element that require a hover class
         * @param {Boolean} isHoverIn
         * @public
         * @returns {void}
         */
        hoverEffect: function (target, hoverElement, isHoverIn) {
            var sliderWidth = this.element.width(),
                sliderHeight = this.element.height();

            this.overlaySize = sliderWidth > sliderHeight ? sliderWidth * 1.5 : sliderHeight * 1.5;

            target.css({
                'width': isHoverIn ? this.overlaySize : '0',
                'height': isHoverIn ? this.overlaySize : '0'
            });

            if (hoverElement) {
                hoverElement.toggleClass(this.options.classes.hover, isHoverIn);
            }
        },

        /**
         * @param {Object} element
         * @public
         * @returns {void}
         */
        showMoreEvent: function (element) {
            var self = this,
                options = self.options;

            $(options.selectors.showMoreButton, element)
                .off('click.amBanner')
                .on('click.amBanner', function () {
                self.hoverEffect($(this).next().find(self.options.selectors.overlayBlock), element, !$(this).hasClass(options.classes.active));

                if (options.sliderAnimationEffect !== options.animationEffects.split) {
                    self.slickAutoplay(element, $(this).hasClass(options.classes.active));
                }

                $(this).toggleClass(options.classes.active)
                    .closest(options.selectors.sliderItem)
                    .toggleClass(options.classes.active);
            });
        },

        /**
         * @param {Object} element
         * @public
         * @returns {void}
         */
        clearHover: function (element) {
            element.removeClass(this.options.classes.hover)
                .find(this.options.selectors.showMoreButton)
                .removeClass(this.options.classes.active)
                .closest(this.options.selectors.sliderItem)
                .removeClass(this.options.classes.active);
        },

        /**
         * Auto play banners if state is true, stop playing if state is false
         * @param {Object} element
         * @param {Boolean} state
         * @public
         * @returns {void}
         */
        slickAutoplay: function (element, state) {
            if (this.options.isAutoplay) {
                state ? element.slick('slickPlay') : element.slick('slickPause');
            }
        },

        /**
         * @private
         * @returns {Object}
         */
        _getMergedOptions: function (restOptions) {
            return _.extend(this.options.baseSliderOptions, this.options.sliderOptions, restOptions);
        },

        /**
         * @private
         * @returns {void}
         */
        _destroySlider: function () {
            if (this.element.hasClass('slick-initialized')) {
                this.element.slick('unslick');
            }

            this.element.css('max-width', 'inherit').removeClass(this.options.classes.loaded);
            this.element.closest(this.options.selectors.sliderWrapper).css('max-width', 'inherit');
        },

        /**
         * @public
         * @returns {void}
         */
        initSlider: function (element, options) {
            element.css('max-width', this.options.sliderBlockWidth).slick(this._getMergedOptions(options));
            element.closest(this.options.selectors.sliderWrapper).css('max-width', this.options.sliderBlockWidth);
            element.addClass(this.options.classes.loaded);
        }
    });

    return $.am.bannerSlider;
});
