var $autocompletes = $('input.Autocomplete');

$autocompletes.wrap('<div class="Autocomplete-InputWrapper"></div>');
$autocompletes.each(function() {
  var $autocomplete = $(this);
  /* append an X for clearing field */
  $autocomplete
  .closest('.Autocomplete-InputWrapper')
  .append('<span class="Autocomplete-Clear"></span>')
  .find('.Autocomplete-Clear')
  .click(function(e){
    var $field = $(this).closest('.Autocomplete-InputWrapper').find('input');
    $field
    .removeAttr('data-last')
    .removeAttr('data-last-entered')
    .removeAttr('data-autocomplete-entered')
    .removeAttr('data-json')
    .val('');
    var $results = $field.closest('.Autocomplete-InputWrapper').find('.Autocomplete-Results');
    $results.html('');
  });
  /* append results wrapper and configure it */
  $autocomplete
  .closest('.Autocomplete-InputWrapper')
  .append('<div class="Autocomplete-Results"></div>')
  .find('.Autocomplete-Results').hide()
  .attr('aria-role','menu')
  /* event listener for clicks in the results list */
  .click(function(e){
    //when results are clicked, select whatever was clicked
    var $result = $(e.target);
    if (!$result.is('.result')) {
      $result = $result.closest('.result');
    }
    if ($result.attr('data-value')) {
      select($autocomplete,$result);
    }
  });
});

var select = function ($field,$result) {
  var $results = $field.closest('.Autocomplete-InputWrapper').find('.Autocomplete-Results');
  var $dummy = $field.closest('.Autocomplete-InputWrapper').find('.Autocomplete-Dummy');
  //mark which result is now selected
  $results.find('.result').removeAttr('aria-selected');
  $result.attr('aria-selected','true');
  //trigger completion
  $field.val($result.attr('data-value'));
  $field.attr('data-autocomplete-entered',$field.val());
  $field.attr('data-json',$result.attr('data-json'));
  $field.trigger('autocomplete');
  $dummy.show();
  $dummy.focus();
}

var update = function (obj) {
  var $this = $(obj);
  var val = $this.val();
  var last = $this.attr('data-last');
  var $results = $this.closest('.Autocomplete-InputWrapper').find('.Autocomplete-Results');
  $results.show('fast');
  $this.attr('data-last',val);
  if (val != '' && val != last) {
    $this.attr('data-last-entered',val);
    AjaxForm.ajax(
      obj,
      {'q': val},
      function(data){
        /* begin handling results */
        $results.html('');
        //there are results
        if (typeof data.r !== 'undefined') {
          for (var value in data.r) {
            if (data.r.hasOwnProperty(value)) {
              $results.append('<div class="result" aria-role="menuitem" data-value="'+value+'">'+data.r[value].label+'</div>');
              $results.find(".result:last-child").attr('data-json',data.r[value].json);
            }
          }
          //select first result
          $results.attr('data-selected',0);
          $results.find('.result:first-child').attr('aria-selected','true');
        }
        //there is an error
        else if (typeof data.e !== 'undefined') {
          $results.append('<div class="error">'+data.e+'</div>');
        }
        //some sort of other problem
        else {
          $results.append('<div class="error">unknown error</div>');
        }
        //update position of results
        updateResultsPosition($results);
        /* end handling results */
      }
    );
  }
}

var updateResultsPosition = function ($results) {
  //how far the bottom of the results is from the window edge
  //if offset is > 0 we need to scroll down
  var offset = $results.offset().top + $results.height() - $(window).scrollTop() - $(window).height()
  if (offset > 0) {
    var scrollTo = $(window).scrollTop() + offset;
    $('html,body').animate({scrollTop: scrollTo}, 500);
  }
}

$autocompletes.keydown(function(e){
  var $this = $(this);
  var $results = $this.closest('.Autocomplete-InputWrapper').find('.Autocomplete-Results');
  var keyCode = e.keyCode || e.which;
  //check for control key presses
  switch (keyCode) {
    case 13://enter
      $this.blur();
      $results.find('.result[aria-selected="true"]').click();
      e.preventDefault();
      return false;
    case 9://tab
      $results.find('.result[aria-selected="true"]').click();
      return true;
    case 40://down
      var $options = $results.find('.result');
      var selected = (parseInt($results.attr('data-selected'))+1) % $options.length;
      $results.attr('data-selected',selected);
      $results.find('.result').removeAttr('aria-selected');
      $results.find('.result').eq(selected).attr('aria-selected',true);
      e.preventDefault();
      return false;
    case 38://up
      var $options = $results.find('.result');
      var selected = (parseInt($results.attr('data-selected'))-1) % $options.length;
      $results.attr('data-selected',selected);
      $results.find('.result').removeAttr('aria-selected');
      $results.find('.result').eq(selected).attr('aria-selected',true);
      e.preventDefault();
      return false;
  }
});

$autocompletes.keyup(function(e){
  var keyCode = e.keyCode || e.which;
  var skipped = [
    9,13,16,38,40 //built in controls
  ];
  if (skipped.indexOf(keyCode) >= 0) {
    e.preventDefault();
    return false;
  }
  update(this);
});

$autocompletes.focus(function(e){
  var $this = $(this);
  var $results = $this.closest('.Autocomplete-InputWrapper').find('.Autocomplete-Results');
  //restore the last typed value
  if ($this.attr('data-last-entered')) {
    $this.val($this.attr('data-last-entered'));
  }
  //show results and update
  $results.show('fast');
  update(this);
});

$autocompletes.blur(function(e){
  var $this = $(this);
  var $results = $this.closest('.Autocomplete-InputWrapper').find('.Autocomplete-Results');
  $results.find('.result[aria-selected="true"]').click();
  if ($this.attr('data-autocomplete-entered')) {
    $this.val($this.attr('data-autocomplete-entered'));
  }
  $results.hide('slow');
});
