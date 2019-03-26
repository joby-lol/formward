$(function() {
  var $ac = $('.FormwardAjaxAutocomplete');
  //run query from query field, display in results div
  var runQuery = function(query, url, $results) {
    query = query.trim();
    if (query != $results.data('lastquery')) {
      $results.data('lastquery', query);
      url = url.replace('$q', encodeURIComponent(query));
      $results.data('lasturl', url);
      $results.addClass('loading');
      $.ajax({
        url: url,
        dataType: 'json',
        success: function(data, status, xhr) {
          console.log(data);
          console.log(status);
          console.log(xhr);
          //only process if this is the most recent request
          if (xhr.responseURL == $results.data('lasturl')) {
            $results.removeClass('loading');
          }
        }
      });
    }
  }
  //set up autocompletes
  $ac.each(function() {
    //set up all the fields and such that we need
    var $container = $(this);
    var url = atob($container.data('ajaxsource'));
    $container.addClass('js-active');
    var $query = $container.find('input.ajax-field-query');
    var $results = $container.find('div.ajax-field-results');
    var $value = $container.find('select.ajax-field-value');
    $value.hide();
    //add event listeners on query
    var runQueryT = _.throttle(function() {
      runQuery($query.val(), url, $results)
    }, 500, true);
    $query.on('keyup', runQueryT);
    //run query immediately
    runQueryT();
  });
});