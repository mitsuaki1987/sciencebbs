function startup() {
    var el = document.getElementById("canvas");
    el.addEventListener("touchstart", handleStart, false);
    el.addEventListener("touchend", handleEnd, false);
    el.addEventListener("touchcancel", handleCancel, false);
    el.addEventListener("touchmove", handleMove, false);
}

document.addEventListener("DOMContentLoaded", startup);
var ongoingTouches = [];

function handleStart(evt) {
    evt.preventDefault();
    log("touchstart.");
    var el = document.getElementById("canvas");
    var ctx = el.getContext("2d");
    var touches = evt.changedTouches;
    
    for (var i = 0; i < touches.length; i++) {
        log("touchstart:" + i + "...");
        ongoingTouches.push(copyTouch(touches[i]));
        var color = colorForTouch(touches[i]);
        log("touchstart:" + i + ".");
    }
}

function handleMove(evt) {
    evt.preventDefault();
    var el = document.getElementById("canvas");
    var ctx = el.getContext("2d");
    var rect = el.getBoundingClientRect()
    var touches = evt.changedTouches;

    for (var i = 0; i < touches.length; i++) {
        var color = colorForTouch(touches[i]);
        var idx = ongoingTouchIndexById(touches[i].identifier);

        if (idx == 0) {
            log("continuing touch "+idx);
            ctx.beginPath();
            log("ctx.moveTo(" + ongoingTouches[idx].clientX + ", " + ongoingTouches[idx].clientY + ");");
            ctx.moveTo(ongoingTouches[idx].clientX -  rect.left, ongoingTouches[idx].clientY - rect.top);
            log("ctx.lineTo(" + touches[i].clientX + ", " + touches[i].clientY + ");");
            log("ctx.lineTo2(" + rect.left + ", " + rect.top + ");");
            ctx.lineTo(touches[i].clientX - rect.left, touches[i].clientY - rect.top);
            ctx.lineWidth = 4;
            ctx.strokeStyle = color;
            ctx.stroke();

            ongoingTouches.splice(idx, 1, copyTouch(touches[i]));  // swap in the new touch record
            log(".");
        } else {
            log("can't figure out which touch to continue");
        }
    }
}
function handleEnd(evt) {
    evt.preventDefault();
    log("touchend");
    var el = document.getElementById("canvas");
    var ctx = el.getContext("2d");
    var touches = evt.changedTouches;

    for (var i = 0; i < touches.length; i++) {
        var color = colorForTouch(touches[i]);
        var idx = ongoingTouchIndexById(touches[i].identifier);

        if (idx >= 0) {
            ongoingTouches.splice(idx, 1);  // remove it; we're done
        } else {
            log("can't figure out which touch to end");
        }
    }
}
function handleCancel(evt) {
    evt.preventDefault();
    log("touchcancel.");
    var touches = evt.changedTouches;
    
    for (var i = 0; i < touches.length; i++) {
        var idx = ongoingTouchIndexById(touches[i].identifier);
        ongoingTouches.splice(idx, 1);  // remove it; we're done
    }
}
function colorForTouch(touch) {
    var r = touch.identifier % 16;
    var g = Math.floor(touch.identifier / 3) % 16;
    var b = Math.floor(touch.identifier / 7) % 16;
    r = r.toString(16); // make it a hex digit
    g = g.toString(16); // make it a hex digit
    b = b.toString(16); // make it a hex digit
    var color = "#" + r + g + b;
    log("color for touch with identifier " + touch.identifier + " = " + color);
    return color;
}
function copyTouch({ identifier, clientX, clientY }) {
    return { identifier, clientX, clientY };
}

function ongoingTouchIndexById(idToFind) {
    for (var i = 0; i < ongoingTouches.length; i++) {
        var id = ongoingTouches[i].identifier;
        
        if (id == idToFind) {
            return i;
        }
    }
    return -1;    // not found
}
function log(msg) {
    var p = document.getElementById('log');
    p.innerHTML = msg + "\n" + p.innerHTML;
}
