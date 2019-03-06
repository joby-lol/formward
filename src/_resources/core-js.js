/* create AjaxForm object in the global scope, for other scripts to use */
AjaxForm = {};

//debouncing tools
AjaxForm.debounce = function (func, wait, immediate) {
	var timeout;
	return function() {
		var context = this, args = arguments;
		var later = function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if (callNow) func.apply(context, args);
	};
};

//automatically debounced ajax calls
AjaxForm.ajaxDebounced = {};
AjaxForm.ajax = function(object,query,callback) {
	var $wrapper = $(object).closest('.AjaxField-wrapper');
  var token = $wrapper.attr('data-token');
	if (!AjaxForm.ajaxDebounced.hasOwnProperty(token)) {
		AjaxForm.ajaxDebounced[token] = AjaxForm.debounce(function(object,query,callback) {
		  var $wrapper = $(object).closest('.AjaxField-wrapper');
		  var token = $wrapper.attr('data-token');
		  if (!token) {
		    console.log('Ajax Field token could not be found');
		    return;
		  }
		  $wrapper.addClass('loading');
		  post = {
		    'AjaxToken': token,
		    'query': JSON.stringify(query)
		  };
		  $.post(
		    '',
		    post,
		    function(data) {
		      callback(data);
		      $wrapper.removeClass('loading');
		    }
		  );
		},500,false);
	}
	return AjaxForm.ajaxDebounced[token](object,query,callback);
};

//disable/feedback on submit
$(function($) {
  //disable forms on submit
  $('.Form form').submit(function(e){
    $(this).addClass('submitted');
    $(this).find('input,textarea,select').addClass('disabled');
  });
});

//max-length fields
$(function($) {
  //GUI for max-length fields
  var maxLengthFieldUpdate = function($this) {
    var length = $this.val().length;
    var max = $this.attr('max');
    if ($this.val().length > max) {
      $this.val($this.val().substring(0,max));
    }
    $counter = $this.parent().find('.Field-tips-maxlength .maxlength-counter');
    $counter.text(length+'/'+max);
    var pct = length/max;
    if (pct < 0.75) {
      $counter.removeClass('pct-75');
      $counter.removeClass('pct-90');
      $counter.removeClass('pct-100');
    }else {
      if (pct > 0.75) {
        $counter.addClass('pct-75');
      }
      if (pct > 0.90) {
        $counter.addClass('pct-90');
      }
      if (pct > 1) {
        $counter.addClass('pct-100');
      }
    }
  }
  $('.Form *[max]').each(function(){
    var $this = $(this);
    var max = $this.attr('max');
    // $.data(this,'tip',$this.parent().find('.Field-tips-maxlength'));
    $tip = $this.parent().find('.Field-tips-maxlength');
    $tip.append("<div class='maxlength-counter'></div>");
    maxLengthFieldUpdate($(this));
  }).keyup(function(){
    maxLengthFieldUpdate($(this));
  }).change(function(){
    maxLengthFieldUpdate($(this));
  });
});

//file fields
$(function($) {
	//add drag cues to file fields
	var dragTimer;
	$('.Form input[type="file"]')
	.on('dragover',function(e){
	  var dt = e.originalEvent.dataTransfer;
	  if (dt.types && (dt.types.indexOf ? dt.types.indexOf('Files') != -1 : dt.types.contains('Files'))) {
	    $(this).addClass('drag');
	    window.clearTimeout(dragTimer);
	  }
	})
	.on('dragleave',function(e) {
	  var $this = $(this);
	  dragTimer = window.setTimeout(function() {
	    $this.removeClass('drag');
	  }, 25);
	});

	//add filled cues to file fields
	$('.Form input[type="file"]').change(function(){
	  if ($(this).val()) {
	    $(this).addClass('filled').removeClass('drag');
	  }
	});
});
