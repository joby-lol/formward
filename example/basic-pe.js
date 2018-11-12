/**
 * Note that none of the following code is actually REQUIRED for any form fields
 * to work. This code is strictly progressive enhancement to enhance the
 * experience of some of the more complex form fields.
 */
var _sortablelist_el;

function sortablelist_dragOver(e) {
  if (_sortablelist_el.parentNode !== e.target.parentNode)
    return
  if (sortablelist_isBefore(_sortablelist_el, e.target))
    e.target.parentNode.insertBefore(_sortablelist_el, e.target);
  else
    e.target.parentNode.insertBefore(_sortablelist_el, e.target.nextSibling);
}

function sortablelist_dragEnd() {
  _sortablelist_el.classList.remove('dragging');
  _sortablelist_el = null;
}

function sortablelist_dragStart(e) {
  e.dataTransfer.effectAllowed = "move";
  e.dataTransfer.setData("text/plain", null);
  _sortablelist_el = e.target;
  _sortablelist_el.classList.add('dragging');
}

function sortablelist_isBefore(el1, el2) {
  if (el2.parentNode === el1.parentNode)
    for (var cur = el1.previousSibling; cur; cur = cur.previousSibling)
      if (cur === el2)
        return true;
  return false;
}

function sortablelist_isBefore(el1, el2) {
  if (el2.parentNode === el1.parentNode)
    for (var cur = el1.previousSibling; cur; cur = cur.previousSibling)
      if (cur === el2)
        return true;
  return false;
}

document.addEventListener('DOMContentLoaded', function(e) {
  var fields = document.querySelectorAll('.FieldWrapper-formward-ordering-field');
  for (var i = 0; i < fields.length; i++) {
    (function() {
      //get all the elements we need
      var wrapper = fields[i];
      wrapper.classList.add('js-active');
      var input = wrapper.querySelectorAll('.formward-ordering-field')[0];
      var controlWrapper = document.createElement('div');
      var controlList = document.createElement('ul');
      var opts = JSON.parse(input.getAttribute('data-opts'));
      controlWrapper.appendChild(controlList);
      wrapper.insertAdjacentElement('beforeend', controlWrapper);
      //set up the adding tool
      var addWrapper = document.createElement('div');
      addWrapper.classList.add('add-item');
      controlWrapper.appendChild(addWrapper);
      var addField = document.createElement('input');
      addField.setAttribute('placeholder', 'add item');
      addWrapper.appendChild(addField);
      addField.addEventListener('keypress', function(e) {
        if (e.which == 13) {
          e.preventDefault();
          input.value += '\n' + addField.value;
          addField.value = '';
          syncUp();
        }
      });
      /*
      Set up the functions/listeners we need
       */
      //retrieve the values from the actual input
      var inputValues = function() {
        values = input
          .value
          .split(/[\r\n]+/)
          .map(function(e) {
            return e.trim()
          })
          .filter(function(e) {
            return e != '';
          });
        return values;
      }
      //sync down from the state of the control into the actual input
      var syncDown = function() {
        var items = controlList.querySelectorAll('.form-ordering-item');
        var value = [];
        for (var i = 0; i < items.length; i++)
          if (!items[i].classList.contains('deleted'))
            value.push(items[i].getAttribute('data-value'));
          else
            value.push('DELETE:' + items[i].getAttribute('data-value'));
        input.value = value.join('\n');
      }
      //sync up from the state of the actual input into the control's UL
      var syncUp = function() {
        controlList.innerHTML = '';
        values = inputValues();
        values.map(function(k) {
          var deleted = '';
          if (k.substring(0, 7) == 'DELETE:') {
            k = k.substring(7);
            deleted = ' deleted';
          }
          var v = '[' + k + ']';
          if (opts[k]) {
            v = opts[k];
          }
          controlList.innerHTML += '<li class="form-ordering-item' + deleted + '" draggable="true" data-value="' + k + '" ondragend="sortablelist_dragEnd()" ondragover="sortablelist_dragOver(event)" ondragstart="sortablelist_dragStart(event)">' + v + '<a class="delete-button">delete</a></li>';
        });
        controlList.addEventListener('dragend', syncDown);
        //set up event listeners on delete buttons
        var bs = controlList.querySelectorAll('.delete-button');
        for (var i = 0; i < bs.length; i++) {
          bs[i].addEventListener('click', function(e) {
            e.target.parentNode.classList.toggle('deleted');
            syncDown();
          });
        }
      }
      /*
      Run syncUp immediately
       */
      syncUp();
    }());
  }
});