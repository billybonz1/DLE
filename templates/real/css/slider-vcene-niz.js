/*!
 * Ext Core Library 3.0
 * http://extjs.com/
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * 
 * MIT Licensed - http://extjs.com/license/mit.txt
 */
Ext.ns('Ext.ux');

Ext.ux.Carousel = Ext.extend(Ext.util.Observable, {
    interval: 3,
    transitionDuration: 1,
    transitionType: 'carousel',
    transitionEasing: 'easeOut',
    itemSelector: 'img',
    activeSlide: 0,
    autoPlay: false,
    showPlayButton: false,
    pauseOnNavigate: false,
    wrap: false,
    freezeOnHover: false,
    navigationOnHover: false,
    hideNavigation: false,
    width: null,
    height: null,
    staticNavigation: false,
    caption: true,
    debug: false,
    slideGroupType: false,

    constructor: function(elId, config) {
        config = config || {};
        Ext.apply(this, config);

        Ext.ux.Carousel.superclass.constructor.call(this, config);

        this.addEvents(
            'beforeprev',
            'prev',
            'beforenext',
            'next',
            'change',
            'play',
            'pause',
            'freeze',
            'unfreeze'
        );
        this.el = Ext.get(elId);
        this.slides = this.els = [];
        
        if(this.autoPlay || this.showPlayButton) {
            this.wrap = true;
        }

        if(this.autoPlay && typeof config.showPlayButton === 'undefined') {
            this.showPlayButton = true;
        }

        this.initMarkup();
        this.initEvents();
        this.resizeMarkup();
        this.updateStaticNavigation();
        if(this.carouselSize > 0) {
            this.refresh();
        }
        if(this.carouselSize == 1) {
            this.staticNavigation = false;
        }
    },

    initMarkup: function() {
        var dh = Ext.DomHelper;

        this.carouselSize = 0;
        var items = this.el.select(this.itemSelector);
        this.els.container = dh.append(this.el, {cls: 'ux-carousel-container'}, true);
        this.els.slidesWrap = dh.append(this.els.container, {cls: 'ux-carousel-slides-wrap'}, true);

        this.els.navigation = dh.append(this.els.container, {cls: 'ux-carousel-nav'}, true).hide();
        this.els.navNext = dh.append(this.els.navigation, {tag: 'a', href: '#', cls: 'ux-carousel-nav-next'}, true);
        if(this.showPlayButton) {
            this.els.navPlay = dh.append(this.els.navigation, {tag: 'a', href: '#', cls: 'ux-carousel-nav-play'}, true)
        }
        this.els.navPrev = dh.append(this.els.navigation, {tag: 'a', href: '#', cls: 'ux-carousel-nav-prev'}, true);

        // set the dimensions of the container
        this.slideWidth = this.width || this.el.getWidth(true);
        this.slideHeight = this.height || this.el.getHeight(true);
    
    // IE can't calculate width and height of user panel
    if(Ext.isIE7 && this.el.id == 'productsCategory22')
    {
      this.slideWidth = 850;
      this.slideHeight = 180;
    }
    
        this.els.container.setStyle({
            width: this.slideWidth + 'px',
            height: this.slideHeight + 'px'
        });

        items.appendTo(this.els.slidesWrap).each(function(item) {
            item = item.wrap({cls: 'ux-carousel-slide'});
            this.slides.push(item);
            if (this.slideGroupType) {
                item.setWidth(this.slideWidth + 'px').setHeight(this.slideHeight + 'px');
            } else {
                item.setHeight(this.slideHeight + 'px');
            }
        }, this);
        this.carouselSize = this.slides.length;
        items.each(function(item) {
            var innerElWidth = 0;
            Ext.fly(item).select('>div').each(function(t) {
                innerElWidth += (Ext.fly(t).getWidth() + parseFloat(Ext.fly(t).getStyle('marginRight'))+parseFloat(Ext.fly(t).getStyle('marginLeft')));
            });
        }, this);
        if(this.navigationOnHover) {
            this.els.navigation.setStyle('top', (-1*this.els.navigation.getHeight()) + 'px');
        }
        if (this.staticNavigation) {
            this.els.staticNavigation = dh.insertAfter(this.el, {cls: 'ux-carousel-static-nav bg_rep'}, true);
            this.els.staticNavigationList = dh.append(this.els.staticNavigation, {cls: 'nav_promo_slider bg-shadow', tag: 'ul', id: 'staticNavigation'}, true);
            items.each(function(item) {
                var indexToclass = '', captionItem, miniNavItem;
                if(this.caption) {
                    captionItem = item.select('h4 a');
                    miniNavItem = captionItem.elements[0].innerHTML;
                } else {
                    miniNavItem = item.child('img').dom.outerHTML;//item.innerHTML
                }

                var index = items.indexOf(item);
                if (index == 0) {
                    indexToclass += ' first';
                }
                if (index == this.slides.length-1) {
                    indexToclass += ' last';
                }
                if (index == this.activeSlide) {
                    indexToclass += ' active';
                }
                this.els.staticNavigationListItem = dh.append(this.els.staticNavigationList, {cls: indexToclass, tag: 'li', html: '<a href="#"><span>' + miniNavItem + '</span>'}, true);
            }, this);
        }
        this.el.clip();
    },
    resizeMarkup: function () {
        var items = this.el.select('.ux-carousel-slide');
        if (this.width) {
            this.els.container.setStyle({
                width: this.width + 'px',
                height: 340 + 'px'
            });

            if(this.slideGroupType) {
                items.each(function(item) {
                    item.setWidth(this.width + 'px');
                }, this);
                this.els.slidesWrap.setWidth((this.width * this.carouselSize) + 'px');
            }
            var items2 = this.el.select(this.itemSelector);
            if (this.debug && this.width < 1190) {
                items2.each(function(item) {
                    var thisEls = Ext.fly(item).select('>div'),
                        thisElsCount, contentWidth;
                    thisElsCount = thisEls.elements.length;

                    thisEls.each(function(target) {
                        contentWidth = (Ext.fly(target).getWidth(true)+ parseFloat(Ext.fly(target).getStyle('marginRight')))*thisElsCount;
                        if (contentWidth > Ext.fly(item).getWidth()) {
                            var parent = Ext.fly(target).parent('.ux-carousel-slide');
                                nextSlideGroup = Ext.fly(parent).next();
                            if(Ext.fly(item).select('>div.last-move')) {
                                ///Ext.fly(item).select('>div.last-move').appendTo(Ext.fly(nextSlideGroup));
                                ///Ext.fly(item).select('>div.last-move');
                                //Ext.fly(item).select('>div.last-move').removeClass('last-move');
                            }
                        }
                    });
                }, this);
            }
        }
        this.setSlide(this.activeSlide);
    },
    initEvents: function() {
        this.els.navPrev.on('click', function(ev) {
            ev.preventDefault();
            var target = ev.getTarget();
            target.blur();            
            if(Ext.fly(target).hasClass('ux-carousel-nav-disabled')) return;
            this.prev();
        }, this);

        this.els.navNext.on('click', function(ev) {
            ev.preventDefault();
            var target = ev.getTarget();
            target.blur();
            if(Ext.fly(target).hasClass('ux-carousel-nav-disabled')) return;
            this.next();
        }, this);
        if (this.staticNavigation) {
            var items = this.els.staticNavigationList;
            var item = items.select('li');           
            var thisCache = this;
            item.each(function(it) {
                it.on('click', function(ev) {
                    ev.preventDefault();                
                    if(Ext.fly(item).hasClass('active')) return;
                    var parentTarget = Ext.fly(ev.target).parent('li');
                    thisCache.selectedItem(item.indexOf(parentTarget)+1);
                }, this);
            });
            
        }
        if(this.showPlayButton) {
            this.els.navPlay.on('click', function(ev){
                ev.preventDefault();
                ev.getTarget().blur();
                if(this.playing) {
                    this.pause();
                }
                else {
                    this.play();
                }
            }, this);
        };

        if(this.freezeOnHover) {
            this.els.container.on('mouseenter', function(){
                if(this.playing) {
                    this.fireEvent('freeze', this.slides[this.activeSlide]);
                    Ext.TaskMgr.stop(this.playTask);
                }
            }, this);
            this.els.container.on('mouseleave', function(){
                if(this.playing) {
                    this.fireEvent('unfreeze', this.slides[this.activeSlide]);
                    Ext.TaskMgr.start(this.playTask);
                }
            }, this, {buffer: (this.interval/2)*1000});
        };

        if(this.navigationOnHover) {
            this.els.container.on('mouseenter', function(){
                if(!this.navigationShown) {
                    this.navigationShown = true;
                    this.els.navigation.stopFx(false).shift({
                        y: this.els.container.getY(),
                        duration: this.transitionDuration
                    })
                }
            }, this);

            this.els.container.on('mouseleave', function(){
                if(this.navigationShown) {
                    this.navigationShown = false;
                    this.els.navigation.stopFx(false).shift({
                        y: this.els.navigation.getHeight() - this.els.container.getY(),
                        duration: this.transitionDuration
                    })
                }
            }, this);
        }

        if(this.interval && this.autoPlay) {
            this.play();
        };
    },

    prev: function() {
        if (this.fireEvent('beforeprev') === false) {
            return;
        }
        if(this.pauseOnNavigate) {
            this.pause();
        }
        this.setSlide(this.activeSlide - 1);
        this.updateStaticNavigation();
        this.fireEvent('prev', this.activeSlide);
        return this; 
    },
    
    next: function() {
        if(this.fireEvent('beforenext') === false) {
            return;
        }
        if(this.pauseOnNavigate) {
            this.pause();
        }
        this.setSlide(this.activeSlide + 1);
        this.updateStaticNavigation();
        this.fireEvent('next', this.activeSlide);        
        return this;         
    },
    selectedItem: function (index) {
        this.setSlide(index-1);
        this.updateStaticNavigation();
    },
    updateStaticNavigation: function() {
        if (this.staticNavigation) {
            var staticNavigationUl = Ext.get('staticNavigation');
            var items = this.els.staticNavigation.select('li');
            items.each(function(item) {
                var index = items.indexOf(item);
                if (index == this.activeSlide) {
                    item.radioClass('active');
                }                
            }, this);
        }
    },
    play: function() {
        if(!this.playing) {
            this.playTask = this.playTask || {
                run: function() {
                    this.playing = true;
                    this.setSlide(this.activeSlide+1);
                },
                interval: this.interval*1000,
                scope: this
            };
            
            this.playTaskBuffer = this.playTaskBuffer || new Ext.util.DelayedTask(function() {
                Ext.TaskMgr.start(this.playTask);
            }, this);

            this.playTaskBuffer.delay(this.interval*1000);
            this.playing = true;
            if(this.showPlayButton) {
                this.els.navPlay.addClass('ux-carousel-playing');
            }
            this.fireEvent('play');
        }        
        return this;
    },

    pause: function() {
        if(this.playing) {
            Ext.TaskMgr.stop(this.playTask);
            this.playTaskBuffer.cancel();
            this.playing = false;
            if(this.showPlayButton) {
                this.els.navPlay.removeClass('ux-carousel-playing');
            }
            this.fireEvent('pause');
        }        
        return this;
    },
        
    clear: function() {
        this.els.slidesWrap.update('');
        this.slides = [];
        this.carouselSize = 0;
        this.pause();
        return this;
    },
    
    add: function(el, refresh) {
        var item = Ext.fly(el).appendTo(this.els.slidesWrap).wrap({cls: 'ux-carousel-slide'});

        //item.setWidth(this.slideWidth + 'px').setHeight(this.slideHeight + 'px');
    item.setHeight(this.slideHeight + 'px');
        this.slides.push(item);
        if(refresh) {
            this.refresh();
        }
        return this;
    },
    
    refresh: function() {
        this.carouselSize = this.slides.length;
    if(0 == this.carouselSize)
    {
      return;
    }
        var items = this.el.child('.ux-carousel-slide');
        if(!this.slideGroupType) {
            this.els.slidesWrap.setWidth((Ext.fly(items).getWidth() * this.carouselSize) + 'px');
        } else {
            this.els.slidesWrap.setWidth((this.slideWidth * this.carouselSize) + 'px');

        }
        if(this.carouselSize > 0) {
            if(!this.hideNavigation) this.els.navigation.show();
            this.activeSlide = 0;
            this.setSlide(0, true);
        }                
        return this;        
    },
    
    setSlide: function(index, initial) {
        if(!this.wrap && !this.slides[index]) {
            return;
        }
        else if(this.wrap) {
            if(index < 0) {
                index = this.carouselSize-1;
            }
            else if(index > this.carouselSize-1) {
                index = 0;
            }
        }
        if(!this.slides[index]) {
            return;
        }
    
    var visibleItemsCount = Math.round(this.el.getWidth(true) / this.el.child('.ux-carousel-slide').getWidth());
        
    if(!this.slides[index + visibleItemsCount - 1] && this.slides.length >= visibleItemsCount) {
      return;
    }

        var offset;
        var items = this.el.child('.ux-carousel-slide');
        var itemsLast = this.el.child('.ux-carousel-slide:last-child');
        if(this.slideGroupType) {
             if (this.width) {
                offset = index * this.width;
             } else {
                offset = index * this.slideWidth;
             }
        } else {
            offset = index * Ext.fly(items).getWidth();
        }
        
    // if container smaller than visible elements count
    var autoLeftMargin = Math.abs(this.el.getWidth(true) - visibleItemsCount * this.el.child('.ux-carousel-slide').getWidth())/2;
    
    // if container bigger than visible elements count
    if ((this.el.getWidth(true) - visibleItemsCount * this.el.child('.ux-carousel-slide').getWidth()) > 0)
    {
      autoLeftMargin = this.el.getWidth(true) - visibleItemsCount * this.el.child('.ux-carousel-slide').getWidth();
    }
    
    if(Ext.isIE7 && this.slideWidth == 920)
    {
      autoLeftMargin = 30;
    }
    
        if (!initial) {
            switch (this.transitionType) {
                case 'fade':
                    this.slides[index].setOpacity(0);
                    this.slides[this.activeSlide].stopFx(false).fadeOut({
                        duration: this.transitionDuration / 2,
                        callback: function(){
                            this.els.slidesWrap.setStyle('left', (-1 * offset) + 'px');
                            this.slides[this.activeSlide].setOpacity(1);
                            this.slides[index].fadeIn({
                                duration: this.transitionDuration / 2
                            });
                        },
                        scope: this
                    })
                    break;

                default:
                    if(!this.slideGroupType) {
                        var xNew = (-1 * offset) + this.els.container.getX() + autoLeftMargin;
                        this.els.container.removeClass('after-disable');
                        if (this.els.slidesWrap.getWidth()+xNew <= this.el.getWidth()) {
                            this.els.container.addClass('after-disable');
                            return;
                        } else {
                            this.els.slidesWrap.stopFx(false);
                            this.els.slidesWrap.shift({
                                duration: this.transitionDuration,
                                x: xNew,
                                easing: this.transitionEasing
                            });
                        }

                        break;
                    } else {
                        var xNew = (-1 * offset) + this.els.container.getX();
                        this.els.slidesWrap.stopFx(false);
                        this.els.slidesWrap.shift({
                            duration: this.transitionDuration,
                            x: xNew,
                            easing: this.transitionEasing
                        });

                        break;
                    }
                    break;
            }
        }
        else {
            this.els.slidesWrap.setStyle('left', '0');

      var xNew = (-1 * offset) + this.els.container.getX() + autoLeftMargin;
      
      this.els.slidesWrap.stopFx(false);
      this.els.slidesWrap.shift({
        duration: this.transitionDuration,
        x: xNew,
        easing: this.transitionEasing
      });
        }
        this.activeSlide = index;
        this.updateNav();
        this.updateStaticNavigation();
        this.fireEvent('change', this.slides[index], index);
    },

    updateNav: function() {
        this.els.navPrev.removeClass('ux-carousel-nav-disabled');
        this.els.navNext.removeClass('ux-carousel-nav-disabled');
        if(!this.wrap) {
            if(this.activeSlide === 0) {
                this.els.navPrev.addClass('ux-carousel-nav-disabled');
            }
            if(this.activeSlide === this.carouselSize-1) {
                this.els.navNext.addClass('ux-carousel-nav-disabled');
            }
        }
    }
});