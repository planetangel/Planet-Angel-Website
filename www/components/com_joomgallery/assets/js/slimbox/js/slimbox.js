/*!
  Slimbox v1.53 - The ultimate lightweight Lightbox clone
  (c) 2007-2008 Christophe Beyls <http://www.digitalia.be>
  MIT-style license.
  modified by JoomGallery team January 2009
  - automatic resizing
  - use of language constants
  - dynamically ignore of doublets
*/

var Slimbox;

(function() {

  // Global variables, accessible to Slimbox only
  var state = 0, options, images, activeImage, prevImage, nextImage, top, eventKeyDown, fx, preload, preloadPrev = new Image(), preloadNext = new Image(),
  // State values: 0 (closed or closing), 1 (open and ready), 2+ (open and busy with animation)

  // DOM elements
  overlay, center, image, prevLink, nextLink, bottomContainer, bottom, caption, number;

  /*
    Initialization
  */

  window.addEvent("domready", function() {
    eventKeyDown = keyDown.bindWithEvent();

    // Append the Slimbox HTML code at the bottom of the document
    $(document.body).adopt(
      $$([
        overlay = new Element("div", {id: "lbOverlay"}),
        center = new Element("div", {id: "lbCenter"}),
        bottomContainer = new Element("div", {id: "lbBottomContainer"})
      ]).setStyle("display", "none")
    );

    image = new Element("div", {id: "lbImage"}).injectInside(center).adopt(
      prevLink = new Element("a", {id: "lbPrevLink", href: "#"}),
      nextLink = new Element("a", {id: "lbNextLink", href: "#"})
    );
    prevLink.onclick = previous;
    nextLink.onclick = next;

    var closeLink;
    bottom = new Element("div", {id: "lbBottom"}).injectInside(bottomContainer).adopt(
      closeLink = new Element("a", {id: "lbCloseLink", href: "#"}),
      caption = new Element("div", {id: "lbCaption"}),
      number = new Element("div", {id: "lbNumber"}),
      new Element("div", {styles: {clear: "both"}})
    );
    closeLink.onclick = overlay.onclick = close;

    //Edit b2m resize adopted from modified v1.41
    innerImage = new Element('div', {'id': 'innerImage'}).injectInside(image);
    links = new Element('div', {'id': 'links', 'styles': {'display': 'block'}}).injectInside(image);
    prevLink = new Element('a', {'id': 'lbPrevLink', 'href': '#', 'styles': {'display': 'none'}}).injectInside(links);
    nextLink = prevLink.clone().setProperty('id', 'lbNextLink').injectInside(links);
    prevLink.onclick = previous.bind();
    nextLink.onclick = next.bind();
    // End Edit b2m resize

    fx = {
      overlay: overlay.effect("opacity", {duration: 500}).set(0),
      image: image.effect("opacity", {duration: 500, onComplete: nextEffect}),
      bottom: bottom.effect("margin-top", {duration: 400})
    };
  });


  /*
    API
  */

  Slimbox = {
    open: function(_images, startImage, _options) {

      //Edit JoomGallery team flexible resize duration
      if(resizeSpeed > 10){ resizeSpeed = 10;}
      if(resizeSpeed < 1){ resizeSpeed = 1;}
      resizeduration = (11 - resizeSpeed) * 150;
      //Edit JoomGallery team

      options = $extend({
        loop: false,        // Allows to navigate between first and last images
        overlayOpacity: 0.8,      // 1 is opaque, 0 is completely transparent (change the color in the CSS file)
        resizeDuration: resizeduration,     // Duration of each of the box resize animations (in milliseconds)
        resizeTransition: false,    // Default transition in mootools
        initialWidth: 250,      // Initial width of the box (in pixels)
        initialHeight: 250,     // Initial height of the box (in pixels)
        animateCaption: true,
        showCounter: true,      // If true, a counter will only be shown if there is more than 1 image to display

        //Edit b2m resize adopted from modified v1.41
        winWidth: (getWidth()>0) ? getWidth() : 1024,
        winHeight: (getHeight()>0) ? getHeight() : 800,
        //End Edit b2m

        //Edit JoomGallery team flexible language
        counterText: joomgallery_image+" {x} "+joomgallery_of+ "  {y}"    // Translate or change as you wish
        //Edit JoomGallery team
      }, _options || {});

      // The function is called for a single image, with URL and Title as first two arguments
      if (typeof _images == "string") {
        _images = [[_images,startImage]];
        startImage = 0;
      }

      images = _images;
      //remove double objects from image
      //images=images.unique();

      options.loop = options.loop && (images.length > 1);
      position();
      setup(true);
      top = window.getScrollTop() + (window.getHeight() / 15);
      fx.resize = center.effects($extend({duration: options.resizeDuration, onComplete: nextEffect}, options.resizeTransition ? {transition: options.resizeTransition} : {}));
      center.setStyles({top: top, width: options.initialWidth, height: options.initialHeight, marginLeft: -(options.initialWidth/2), display: ""});
      fx.overlay.start(options.overlayOpacity);
      state = 1;
      return changeImage(startImage);
    }
  };

  Element.extend({
    slimbox: function(_options, linkMapper) {
      // The processing of a single element is similar to the processing of a collection with a single element
      $$(this).slimbox(_options, linkMapper);

      return this;
    }
  });

  Elements.extend({
    /*
      options:  Optional options object, see Slimbox.open()
      linkMapper: Optional function taking a link DOM element and an index as arguments and returning an array containing 2 elements:
          the image URL and the image caption (may contain HTML)
      linksFilter:  Optional function taking a link DOM element and an index as arguments and returning true if the element is part of
          the image collection that will be shown on click, false if not. "this" refers to the element that was clicked.
          This function must always return true when the DOM element argument is "this".
    */
    slimbox: function(_options, linkMapper, linksFilter) {
      linkMapper = linkMapper || function(el) {
        return [el.href, el.title];
      };

      linksFilter = linksFilter || function() {
        return true;
      };

      var links = this;

      links.forEach(function(link) {
        link.onclick = function() {
          // Build the list of images that will be displayed
          var filteredLinks = links.filter(linksFilter, this);
          return Slimbox.open(filteredLinks.map(linkMapper), filteredLinks.indexOf(this), _options);
        };
      });

      return links;
    }
  });


  /*
    Internal functions
  */

  function position() {
    overlay.setStyles({top: window.getScrollTop(), height: window.getHeight()});
  }

  function setup(open) {
    ["object", window.ie ? "select" : "embed"].forEach(function(tag) {
      $each(document.getElementsByTagName(tag), function(el) {
        if (open) el._slimbox = el.style.visibility;
        el.style.visibility = open ? "hidden" : el._slimbox;
      });
    });

    overlay.style.display = open ? "" : "none";

    var fn = open ? "addEvent" : "removeEvent";
    window[fn]("scroll", position)[fn]("resize", position);
    document[fn]("keydown", eventKeyDown);
  }

  function keyDown(event) {
    switch(event.code) {
      case 27:  // Esc
      case 88:  // 'x'
      case 67:  // 'c'
        close();
        break;
      case 37:  // Left arrow
      case 80:  // 'p'
        previous();
        break;
      case 39:  // Right arrow
      case 78:  // 'n'
        next();
    }
    // Prevent default keyboard action (like navigating inside the page)
    event.preventDefault();
  }

  function previous() {
    return changeImage(prevImage);
  }

  function next() {
    return changeImage(nextImage);
  }

  function changeImage(imageIndex) {
    if ((state == 1) && (imageIndex >= 0)) {
      state = 2;
      activeImage = imageIndex;
      prevImage = ((activeImage || !options.loop) ? activeImage : images.length) - 1;
      nextImage = activeImage + 1;
      if (nextImage == images.length) nextImage = options.loop ? 0 : -1;

      $$(prevLink, nextLink, image, bottomContainer).setStyle("display", "none");
      fx.bottom.stop().set(0);
      fx.image.set(0);
      center.className = "lbLoading";

      preload = new Image();
      preload.onload = nextEffect;
      preload.src = images[imageIndex][0];
    }

    return false;
  }
  // internal functions for JoomGallery
  // needful to avoid displaying the same picture multiple
  // and the right counter in the slimbox
  // JoomGallery team January 2009

  // analyzes the images array and construct
  // an array with unique numbers
  function joomcheckmulti (images) {
    var o = {};
    for(var i = 0 ; i < images.length; i++) {
      //create an array with unique URL
      //and number of object in images
      o[images[i]["0"]] = i;
    }
    //create an array with the object numbers from o
    var p = new Array();
    for (var i in o) {
      p[o[i]] = true;
    }
    return p;
  }
  // returns the count of all unique pictures
  function joomuniquelength (uniarr) {
    var length=uniarr.length;

    for (var i=0;i<length;i++) {
      if(uniarr[i] != true) {
        length--;
      }
    }
    return length;
  }
  //returns the max. object id of picture in the array
  function joomidmax(uniarr,imlength) {
    var maxid=0;
    for (var i=0;i<=imlength;i++) {
      if(uniarr[i] == true) {
        maxid=Math.max(maxid,i);
      }
    }
    return maxid;
  }
  //returns the count of actual picture showing in the box
  function joomgetactcount (uniarr,imlength,aktcounter){
    var actcount=0;
    for (var i=0;i<=imlength;i++) {
      if(uniarr[i] == true) {
        actcount++;
        if (i==aktcounter) {
          break;
        }
      }
    }
    return actcount;
  }
  // end internal functions for JoomGallery

  function nextEffect() {
    switch (state++) {
      case 2:
        center.className = "";
        image.setStyles({backgroundImage: "url(" + images[activeImage][0] + ")", display: ""});

        //Edit b2m resize adopted from modified v1.41
        if(resizeJsImage==1) {
          if(preload.width>(options.winWidth-40)) {
            preload.height = (preload.height * (options.winWidth-40))/preload.width;
            preload.width = options.winWidth-40;
          }
          if(preload.height>(options.winHeight-150)) {
            preload.width = (preload.width * (options.winHeight-150))/preload.height;
            preload.height = options.winHeight-150;
          }
          var innerImageHtml = "<img src=\""+images[activeImage][0]+"\" width=\""+preload.width+"px\" height=\""+preload.height+"px\" />";
          innerImage.setHTML(innerImageHtml);
        } else {
          image.style.backgroundImage = 'url('+images[activeImage][0]+')';
        }
        //End Edit b2m resize

        $$(image, bottom).setStyle("width", preload.width);
        $$(image, prevLink, nextLink).setStyle("height", preload.height);

        caption.setHTML(images[activeImage][1] || "");

        //edit JoomGallery team
        //check multiple links for correction of the counter
        //return an array with unique object keys
        var uniquearr = new Array();
        uniquearr=joomcheckmulti(images);
        var uniquecount=joomuniquelength(uniquearr);
        var uniquemaxid=joomidmax(uniquearr,images.length);

        //check if a double deleted image and jump to the right one
        var changed =false;
        while(uniquearr[activeImage]!=true) {
          activeImage++;
          changed=true;
          prevImage--;
          nextImage++;
        }
        while(uniquearr[prevImage]!=true && prevImage >= 0) {
          prevImage--;
        }
        if (changed) {
          while(uniquearr[nextImage]!=true && nextImage <= uniquemaxid) {
            nextImage++;
          }
          if (nextImage > uniquemaxid){
            nextImage=-1;
          }
        }
        //get the right counter of actual image
        if (prevImage < 0) {
          imageactcounter=1;
        } else {
          var imageactcounter=joomgetactcount(uniquearr,images.length,activeImage);
        }

        number.setHTML((options.showCounter && (images.length > 1)) ? options.counterText.replace(/{x}/,imageactcounter).replace(/{y}/, uniquecount) : "");

        //no preloading of the neighbours to suppress increasing the image counter
        //if (prevImage >= 0) preloadPrev.src = images[prevImage][0];
        //if (nextImage >= 0) preloadNext.src = images[nextImage][0];

        // end edit JoomGalleryteam

        if (center.clientHeight != image.offsetHeight) {
          fx.resize.start({height: image.offsetHeight});
          break;
        }
        state++;
      case 3:
        if (center.clientWidth != image.offsetWidth) {
          fx.resize.start({width: image.offsetWidth, marginLeft: -image.offsetWidth/2});
          break;
        }
        state++;
      case 4:
        bottomContainer.setStyles({top: top + center.clientHeight, marginLeft: center.style.marginLeft, visibility: "hidden", display: ""});
        fx.image.start(1);
        break;
      case 5:
        if (prevImage >= 0) prevLink.style.display = "";
        if (nextImage >= 0) nextLink.style.display = "";
        if (options.animateCaption) {
          fx.bottom.set(-bottom.offsetHeight).start(0);
        }
        bottomContainer.style.visibility = "";
        state = 1;
    }
  }

  function close() {
    if (state) {
      state = 0;
      preload.onload = Class.empty;
      for (var f in fx) fx[f].stop();
      $$(center, bottomContainer).setStyle("display", "none");
      fx.overlay.chain(setup).start(0);
    }

    return false;
  }

})();



// AUTOLOAD CODE BLOCK (MAY BE CHANGED OR REMOVED)
Slimbox.scanPage = function() {
  var links = $$("a").filter(function(el) {
    return el.rel && el.rel.test(/^lightbox/i);
  });
  $$(links).slimbox({/* Put custom options here */}, null, function(el) {
    return (this == el) || ((this.rel.length > 8) && (this.rel == el.rel));
  });
};
window.addEvent("domready", Slimbox.scanPage);

