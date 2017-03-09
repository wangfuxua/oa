/*
 *
 *
 *
 */

function setTab(name, cursel, n) {
    for (i = 1; i <= n; i++) {
        var tabs = document.getElementById(name + i);
        var tabsCon = document.getElementById(name + i + "_con");
        tabs.className = i == cursel ? "on" : "";
        tabsCon.style.display = i == cursel ? "block" : "none";
    }
}