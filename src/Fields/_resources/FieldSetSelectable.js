(() => {
    var selector = document.getElementById('{{selectorID}}');
    var wrapperIDs /**allWrappers**/ ;
    var allWrappers = {};
    for (const k in wrapperIDs) {
        if (wrapperIDs.hasOwnProperty(k)) {
            allWrappers[k] = document.getElementById(wrapperIDs[k]);
            allWrappers[k].style.marginBottom = '0';
        }
    }
    var selectionChange = function () {
        var selected = allWrappers[selector.value];
        for (const k in allWrappers) {
            if (allWrappers.hasOwnProperty(k)) {
                allWrappers[k].classList.add('hidden');
            }
        }
        selected.classList.remove('hidden');
    };
    selectionChange();
    selector.onchange = selectionChange;
})();