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
        for (const k in allWrappers) {
            if (allWrappers.hasOwnProperty(k)) {
                allWrappers[k].classList.add('hidden');
            }
        }
        if (!allWrappers.hasOwnProperty(selector.value)) {
            return;
        }
        var selected = allWrappers[selector.value];
        selected.classList.remove('hidden');
    };
    selectionChange();
    selector.onchange = selectionChange;
})();