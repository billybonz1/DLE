Ext.onReady(function() {

    var Placeholder = function (selector) {
        var init = function(selector) {
            var elements = Ext.select(selector);
            if (elements.elements.length > 0) {
                Ext.each(elements.elements, function(el) {
                    el = Ext.get(el);
                    var placeholder = el.dom.getAttribute('placeholder');
                    if (placeholder) {
                        el.set({hintData: placeholder, value: placeholder});
                        el.dom.removeAttribute('placeholder');
                        el.on('blur', self.setHint, this);
                        el.on('focus', self.removeHint, this);
                        el.addClass('has-placeholder');
                    }
                });
            }
        };
        this.setHint = function(event, target) {
            if(Ext.isEmpty(target.value))
            {
                target.value = target.getAttribute('hintData');
                Ext.fly(target).removeClass('filled').addClass('has-placeholder');
            }
        };
        this.removeHint = function(event, target) {
            Ext.fly(target).addClass('filled').removeClass('has-placeholder');
            if(target.value == target.getAttribute('hintData'))
            {
                target.value = '';
            }
        };
        init(selector);
    };
    Placeholder('#dsticker-pad input[type="text"]');
  
  Ext.namespace('Vcene');
  Vcene.Cookie = Ext.extend(Ext.util.Observable, {
    set: function(name, value, days)
    {
      var date = new Date(), expires = '';
      if(days)
      {
        date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
        expires = '; expires=' + date.toGMTString();
      }
      document.cookie = name + '=' + value + expires + '; path=/';
    },
    get: function(name)
    {
      name += '=';
      for(var i = 0, c = document.cookie.split(';'); i < c.length; i++)
      {
        while(c[i].charAt(0) == ' ')
        {
          c[i] = c[i].substring(1, c[i].length);
        }
        if(c[i].indexOf(name) === 0)
        {
          return c[i].substring(name.length, c[i].length);
        }
      }
      return null;
    },
    del: function(name)
    {
      this.set(name, '', -1);
    }
  });
  
  UserSubscribe = function (subscribeInfo) {
      //private
      var defaultErrorStruct = {default_field: 'Произошла ошибка при подписке'};
      var alreadyRegMsg = 'Вы уже <br/> подписались на Вцене<br/>';
      //var vkRegUrl = 'https://oauth.vk.com/authorize?client_id=3238031&scope=notes&response_type=code&redirect_uri='+host+'user/subscribe_vk/?callback_url=';
      var getScrollTop = function() {
      var body = Ext.getBody();
      return body.getScroll().top;
      };
        var collectRegFields = function(notEmptyFields) {
            var params = {};
            Ext.each(Ext.select(Ext.query('input.subscribe-data')).elements, function(el) {
                if (el.value && !Ext.fly(el).hasClass('has-placeholder')) {
                    params[el.name] = el.value;
                }
            });
            params['reg_url'] = window.location.href;
            var valid = true;
            while(notEmptyFields.length>0){
                var fieldName = notEmptyFields.pop();
                if (!params[fieldName]) {
                    markErrorFields(defaultErrorStruct[fieldName]);
                    valid = false;
                }
            }
            if (valid) {
                return params;
            } else {
                return false;
            }
        };
      var sendRegMail = function(params) {
        clearErrorFields();
      Ext.Ajax.request({
        url: '/user/register/',
        params: params,
        method:'POST',
        success: function(answer) {
          var answer = Ext.util.JSON.decode(answer.responseText);
          processAnswer(answer);
        }
      });
      };
      var processAnswer = function(answer) {
      if (!answer.success) {
                if (answer.errors) {
                    markErrorFields(answer.errors);
                    return;
                }
                regSuccess(answer.errors['service']);
            } else {
                regSuccess('EMAIL');
      }
      };
      var clearErrorFields = function(errors) {
        Ext.select('.error-for-input').removeClass('visible-error');
      };



      var markErrorFields = function(errors) {
        for( var id in errors) {
          Ext.get(id).update(errors[id]).addClass('visible-error');
        }
      };
      var regSuccess = function(type) {
        switch(type) {
          //case 'VK':
          case 'CONFIRMED':
            Ext.get('reg-msg').hide();
            break;
          case 'LOGIN':
            Ext.get('reg-status').update(alreadyRegMsg);
            Ext.get('reg-msg').hide();
            break;
          case 'CONFIRM':
            Ext.get('reg-status').update(alreadyRegMsg);
            break;
          case 'EMAIL':
          default:
            break;
        }
        
        var dstickerSuccessFrame = Ext.get('dsticker-success-frame');
        dstickerSuccessFrame && dstickerSuccessFrame.show();
        var dstickerRegFrame = Ext.get('dsticker-reg-frame');
        dstickerRegFrame && dstickerRegFrame.hide();
        var dstickerFrame = Ext.get('dsticker-frame');
        dstickerFrame && dstickerFrame.hide();
        var dstickerPad = Ext.get('dsticker-pad');
        dstickerPad && (!dstickerPad.hasClass('visible')) && dstickerPad.addClass('visible');
        var dstickerPopSuccessPad = Ext.get('dsticker-pop-success-pad');
        dstickerPopSuccessPad && dstickerPopSuccessPad.setTop(getScrollTop()) && dstickerPopSuccessPad.show();
        var dstickerPopPad = Ext.get('dsticker-pop-pad');
        dstickerPopPad && dstickerPopPad.hide();
      };
      var sendRegVk = function() {
        window.location.href = vkRegUrl + window.location.href;
      };
      var isEmpty = function(element) {
        var type = typeof(element);
        switch(type) {
          case 'object':
             for(var prop in element) {
               if(element.hasOwnProperty(prop)) {
                 return false;
               }
             }
             return true;
            break;
          default:
            return !Boolean(element)
            break;
        }
      };
      var checkPopupConditions = function() {
      var extractedParams = {};
      if (window.location.search.indexOf('?') === 0) {
        extractedParams = Ext.urlDecode(window.location.search.slice(1));
      }
      if (
        typeof(extractedParams['utm_source']) == 'string' && 
          ((typeof(extractedParams['utm_medium']) == 'string' && extractedParams['utm_medium'] == 'cpc' && (extractedParams['utm_source'] == 'google' || extractedParams['utm_source'] == 'adwords'))
          || (extractedParams['utm_source'] == 'email'))
        ){
        return false;
      } else {
        return true;
      }
      };
      var initFromSession = function(subscribeInfo) {
        if (subscribeInfo && typeof(subscribeInfo) != 'undefined' && !isEmpty(subscribeInfo)) {
          processAnswer(subscribeInfo);
        }
      };
      var initTimeoutPopup = function() {
        var popUp = Ext.get('dsticker-pop-pad');
        var cookies = new Vcene.Cookie();
        var isSubscribed = cookies.get('user_subscribed');
        var pattern = new RegExp('^'+window.location.origin+'/sales', 'i');
        popUp && !pattern.test(document.referrer) && checkPopupConditions() && (isSubscribed != 1) && setTimeout(function(){
          popUp.setTop(getScrollTop());
          popUp.show();
        },10000);
      };
      //public
      //---
    //init
      (function (){
        var dstickerToggle = Ext.get('dsticker-toggle');
        dstickerToggle && dstickerToggle.on('click', function(e, t) {
          Ext.get('dsticker-pad').toggleClass('visible');
        });
        var dstickerClose = Ext.get('dsticker-close');
        dstickerClose && dstickerClose.on('click', function() {
                Ext.get('dsticker-pad').removeClass('visible');
            });

        var dstickerGo = Ext.get('dsticker-go');
        dstickerGo && dstickerGo.on('click', function(e, t) {
          Ext.get('dsticker-reg-frame').show();
          Ext.get('dsticker-frame').hide();
        });
        var dstickerRegMail = Ext.get('dsticker-reg-mail');
        dstickerRegMail && dstickerRegMail.on('click', function(e, t) {
          var params = collectRegFields(['email']);
          if (params) {
            sendRegMail(params);
          }
          return false;
        });
        /*
        var dstickerRegVk = Ext.get('dsticker-reg-vk');
        dstickerRegVk && dstickerRegVk.on('click', function(e, t) {
          sendRegVk();
        });
        */
        var dstickerRegFrameInput = Ext.select('#dsticker-reg-frame input.data, #dsticker-popup-frame input.subscribe-data');
        dstickerRegFrameInput && dstickerRegFrameInput.on('keyup', function(e, t) {
          if (e.keyCode == 13) {
            var params = collectRegFields(['email']);
            if (params) {
              sendRegMail(params);
            }
            return false;
          }
        });
        var dstickerPopSuccessPad = Ext.get('dsticker-pop-success-pad');
        dstickerPopSuccessPad && dstickerPopSuccessPad.setVisibilityMode(Ext.Element.DISPLAY);
        var dstickerPopPad = Ext.get('dsticker-pop-pad');
        dstickerPopPad && dstickerPopPad.setVisibilityMode(Ext.Element.DISPLAY);
        var closePopup = Ext.select('.close-popup');
        closePopup && closePopup.on('click', function(e, t) {
          Ext.get(this).parent().hide();
        });
      }());
      initTimeoutPopup();
      initFromSession(subscribeInfo);
  };
  
  UserSubscribe(subscribeInfo);
});
