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
    var el = document.getElementById("canvas");
    var ctx = el.getContext("2d");
    var touches = evt.changedTouches;
    
    for (var i = 0; i < touches.length; i++) {
        ongoingTouches.push(copyTouch(touches[i]));
    }
}

function handleMove(evt) {
    evt.preventDefault();
    var el = document.getElementById("canvas");
    var ctx = el.getContext("2d");
    var rect = el.getBoundingClientRect()
    var touches = evt.changedTouches;
    var linecolor = document.getElementById('linecolor').color.value;
    
    for (var i = 0; i < touches.length; i++) {
        var idx = ongoingTouchIndexById(touches[i].identifier);

        if (idx == 0) {
            ctx.beginPath();
            ctx.moveTo(ongoingTouches[idx].clientX -  rect.left, ongoingTouches[idx].clientY - rect.top);
            ctx.lineTo(touches[i].clientX - rect.left, touches[i].clientY - rect.top);
            ctx.lineWidth = 4;
            ctx.strokeStyle = linecolor;
            ctx.stroke();

            ongoingTouches.splice(idx, 1, copyTouch(touches[i]));  // swap in the new touch record
        } else {
        }
    }
}
function handleEnd(evt) {
    evt.preventDefault();
    var el = document.getElementById("canvas");
    var ctx = el.getContext("2d");
    var touches = evt.changedTouches;

    //log("touchend");
    for (var i = 0; i < touches.length; i++) {
        var idx = ongoingTouchIndexById(touches[i].identifier);

        if (idx >= 0) {
            ongoingTouches.splice(idx, 1);  // remove it; we're done
        } else {
        }
    }
}
function handleCancel(evt) {
    evt.preventDefault();
    var touches = evt.changedTouches;
    
    for (var i = 0; i < touches.length; i++) {
        var idx = ongoingTouchIndexById(touches[i].identifier);
        ongoingTouches.splice(idx, 1);  // remove it; we're done
    }
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
function clearCanvas() {
    var el = document.getElementById("canvas");
    var ctx = el.getContext("2d");

    ctx.clearRect(0, 0, el.width, el.height);
}
function resizeCanvas() {
    var el = document.getElementById("canvas");
    var width = document.getElementById('width').value;
    var height = document.getElementById('height').value;

    el.setAttribute("width", width);
    el.setAttribute("height", height);
}
function log(msg) {
  var p = document.getElementById('log');
  p.innerHTML = msg + "\n" + p.innerHTML;
}

window.onload = function() {
  document.getElementById('canvassubmit').onclick = function() {
    post();
  };

};

function post() {
    var fd = new FormData();
    
    var submittype = document.getElementById('submittype').subtype.value;
    fd.append('submittype',submittype);

    var name = document.getElementById('submitname').value;
    fd.append('name',name);

    if(submittype == "text"){
        var comment = document.getElementById('comment').value;
        fd.append('comment', comment);
    }
    else if(submittype == "image"){
        const file = document.getElementById("file").files[0];
        fd.append('avatar', file);
    }
    else if(submittype == "file"){
        const file = document.getElementById("file").files[0];
        fd.append('avatar', file);
    }
    else if(submittype == "freehand"){
        img_url = canvas.toDataURL("image/png").replace(new RegExp("data:image/png;base64,"),"");
        fd.append('comment',img_url);
    }
    
    const param = {
        method: "POST",
        body: fd
    }
    fetch("./index.php", param);
    window.location.reload();
}
