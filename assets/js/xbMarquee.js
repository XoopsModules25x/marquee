/* ***** BEGIN LICENSE BLOCK *****
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is Netscape code.
 *
 * The Initial Developer of the Original Code is
 * Netscape Corporation.
 * Portions created by the Initial Developer are Copyright (C) 2002
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s): Doron Rosenberg <doron@netscape.com>
 *                 Bob Clary <bclary@netscape.com>
 *
 * Alternatively, the contents of this file may be used under the terms of
 * either the GNU General Public License Version 2 or later (the "GPL"), or
 * the GNU Lesser General Public License Version 2.1 or later (the "LGPL"),
 * in which case the provisions of the GPL or the LGPL are applicable instead
 * of those above. If you wish to allow use of your version of this file only
 * under the terms of either the GPL or the LGPL, and not to allow others to
 * use your version of this file under the terms of the MPL, indicate your
 * decision by deleting the provisions above and replace them with the notice
 * and other provisions required by the GPL or the LGPL. If you do not delete
 * the provisions above, a recipient may use your version of this file under
 * the terms of any one of the MPL, the GPL or the LGPL.
 *
 ***** END LICENSE BLOCK ***** */

function XbMarquee(id, height, width, scrollAmount, scrollDelay, direction, behavior, html) {
    this.id = id;
    this.scrollAmount = scrollAmount ? scrollAmount : 6;
    this.scrollDelay = scrollDelay ? scrollDelay : 85;
    this.direction = direction ? direction.toLowerCase() : 'left';
    this.behavior = behavior ? behavior.toLowerCase() : 'scroll';
    this.name = 'XbMarquee_' + (++XbMarquee._name);
    this.runId = null;
    this.html = html;
    this.isHorizontal = ('up,down'.indexOf(this.direction) == -1);

    if (typeof(height) == 'number') {
        this.height = height;
        this.heightUnit = 'px';
    }
    else if (typeof(height) == 'string') {
        this.height = parseInt('0' + height, 10);
        this.heightUnit = height.toLowerCase().replace(/^[0-9]+/, '');
    }
    else {
        this.height = 100;
        this.heightUnit = 'px';
    }

    if (typeof(width) == 'number') {
        this.width = width;
        this.widthUnit = 'px';
    }
    else if (typeof(width) == 'string') {
        this.width = parseInt('0' + width, 10);
        this.widthUnit = width.toLowerCase().replace(/^[0-9]+/, '');
    }
    else {
        this.width = 100;
        this.widthUnit = 'px';
    }

    // XbMarquee UI events
    this.onmouseover = null;
    this.onmouseout = null;
    this.onclick = null;
    // XbMarquee state events
    this.onstart = null;
    this.onbounce = null;

    var markup = '';

    if (document.layers) {
        markup = '<ilayer id="' + this.id + 'container" name="' + this.id + 'container" ' +
            'height="' + height + '" ' +
            'width="' + width + '"  ' +
            'clip="' + width + ', ' + height + '" ' +
            '>' +
            '<\/ilayer>';
    }
    else if (document.body && typeof(document.body.innerHTML) != 'string') {
        markup = '<div id="' + this.id + 'container" name="' + this.id + 'container" ' +
            'style="position: relative; overflow: scroll; ' +
            'height: ' + this.height + this.heightUnit + '; ' +
            'width: ' + this.width + this.widthUnit + '; ' +
            'clip: rect(0px, ' + this.width + this.widthUnit + ', ' + this.height + this.heightUnit + ', 0px); ' +
            '">' +
            '<div id="' + this.id + '" style="position:relative;' +
            (this.isHorizontal ? 'width:0px;' : '') + // if we scroll horizontally, make the text container as small as possible
            '">' +
            (this.isHorizontal ? '<nobr>' : '') +
            this.html +
            (this.isHorizontal ? '<\/nobr>' : '') +
            '<\/div>' +
            '<\/div>';
    }
    else {
        markup = '<div id="' + this.id + 'container" name="' +
            this.id + 'container" ' +
            'style="position: relative; overflow: hidden; ' +
            'height: ' + this.height + this.heightUnit + '; ' +
            'width: ' + this.width + this.widthUnit + '; ' +
            'clip: rect(0px, ' + this.width + this.widthUnit + ', ' + this.height + this.heightUnit + ', 0px); ' +
            '">' +
            '<\/div>';
    }
    document.write(markup);

    window[this.name] = this;

}

// Class Properties/Methods

XbMarquee._name = -1;

XbMarquee._getInnerSize = function (elm, propName) {
    var val = 0;

    if (document.layers) {
        // navigator 4
        val = elm.document[propName];
    }
    else if (elm.style && typeof(elm.style[propName]) == 'number') {
        // opera
        // bug in Opera 6 width/offsetWidth. Use clientWidth
        if (propName == 'width' && typeof(elm.clientWidth) == 'number')
            val = elm.clientWidth;
        else
            val = elm.style[propName];
    }
    else {
        //mozilla and IE
        switch (propName) {
            case 'height':
                if (typeof(elm.offsetHeight) == 'number')
                    val = elm.offsetHeight;
                break;
            case 'width':
                if (typeof(elm.offsetWidth) == 'number')
                    val = elm.offsetWidth;
                break;
        }
    }

    return val;

};

XbMarquee.getElm = function (id) {
    var elm = null;
    if (document.getElementById) {
        elm = document.getElementById(id);
    }
    else {
        elm = document.all[id];
    }
    return elm;
};

XbMarquee.dispatchUIEvent = function (event, marqueeName, eventName) {
    var marquee = window[marqueeName];
    var eventAttr = 'on' + eventName;
    if (!marquee) {
        return false;
    }

    if (!event && window.event) {
        event = window.event;
    }

    switch (eventName) {
        case 'mouseover':
        case 'mouseout':
        case 'click':
            if (marquee[eventAttr])
                return marquee['on' + eventName](event);
    }

    return false;
};

XbMarquee.createDispatchEventAttr = function (marqueeName, eventName) {
    return 'on' + eventName + '="XbMarquee.dispatchUIEvent(event, \'' + marqueeName + '\', \'' + eventName + '\')" ';
};

// Instance properties/methods

XbMarquee.prototype.start = function () {
    var markup = '';

    this.stop();

    if (!this.dirsign) {
        if (!document.layers) {
            this.containerDiv = XbMarquee.getElm(this.id + 'container');

            if (typeof(this.containerDiv.innerHTML) != 'string') {
                return;
            }

            // adjust the container size before inner div is filled in
            // so IE will not hork the size of percentage units
            var parentNode = null;
            if (this.containerDiv.parentNode)
                parentNode = this.containerDiv.parentNode;
            else if (this.containerDiv.parentElement)
                parentNode = this.containerDiv.parentElement;

            if (parentNode &&
                typeof(parentNode.offsetHeight) == 'number' &&
                typeof(parentNode.offsetWidth) == 'number') {
                if (this.heightUnit == '%') {
                    this.containerDiv.style.height =
                        parentNode.offsetHeight * (this.height / 100) + 'px';
                }

                if (this.widthUnit == '%') {
                    this.containerDiv.style.width =
                        parentNode.offsetWidth * (this.width / 100) + 'px';
                }
            }

            markup += '<div id="' + this.id + '" name="' + this.id + '" ' +
                'style="position:relative; visibility: hidden;' +
                (this.isHorizontal ? 'width:0px;' : '') + // if we scroll horizontally, make the text container as small as possible
                '" ' +
                XbMarquee.createDispatchEventAttr(this.name, 'mouseover') +
                XbMarquee.createDispatchEventAttr(this.name, 'mouseout') +
                XbMarquee.createDispatchEventAttr(this.name, 'click') +
                '>' +
                (this.isHorizontal ? '<nobr>' : '') +
                this.html +
                (this.isHorizontal ? '<\/nobr>' : '') +
                '<\/div>';

            this.containerDiv.innerHTML = markup;
            this.div = XbMarquee.getElm(this.id);
            this.styleObj = this.div.style;

        }
        else /* if (document.layers) */
        {
            this.containerDiv = document.layers[this.id + 'container'];
            markup =
                '<layer id="' + this.id + '" name="' + this.id + '" top="0" left="0" ' +
                XbMarquee.createDispatchEventAttr(this.name, 'mouseover') +
                XbMarquee.createDispatchEventAttr(this.name, 'mouseout') +
                XbMarquee.createDispatchEventAttr(this.name, 'click') +
                '>' +
                (this.isHorizontal ? '<nobr>' : '') +
                this.html +
                (this.isHorizontal ? '<\/nobr>' : '') +
                '<\/layer>';

            this.containerDiv.document.write(markup);
            this.containerDiv.document.close();
            this.div = this.containerDiv.document.layers[this.id];
            this.styleObj = this.div;
        }

        // Start must not run until the page load event has fired
        // due to Internet Explorer not setting the height and width of
        // the dynamically written content until then
        switch (this.direction) {
            case 'down':
                this.dirsign = 1;
                this.startAt = -XbMarquee._getInnerSize(this.div, 'height');
                this._setTop(this.startAt);

                if (this.heightUnit == '%')
                    this.stopAt = this.height * XbMarquee._getInnerSize(this.containerDiv, 'height') / 100;
                else
                    this.stopAt = this.height;

                break;

            case 'up':
                this.dirsign = -1;

                if (this.heightUnit == '%')
                    this.startAt = this.height * XbMarquee._getInnerSize(this.containerDiv, 'height') / 100;
                else
                    this.startAt = this.height;

                this._setTop(this.startAt);
                this.stopAt = -XbMarquee._getInnerSize(this.div, 'height');

                break;

            case 'right':
                this.dirsign = 1;
                this.startAt = -XbMarquee._getInnerSize(this.div, 'width');
                this._setLeft(this.startAt);

                if (this.widthUnit == '%')
                    this.stopAt = this.width * XbMarquee._getInnerSize(this.containerDiv, 'width') / 100;
                else
                    this.stopAt = this.width;

                break;

            case 'left':
            default:
                this.dirsign = -1;

                if (this.widthUnit == '%')
                    this.startAt = this.width * XbMarquee._getInnerSize(this.containerDiv, 'width') / 100;
                else
                    this.startAt = this.width;

                this._setLeft(this.startAt);
                this.stopAt = -XbMarquee._getInnerSize(this.div, 'width');

                break;
        }
        this.newPosition = this.startAt;
        this.styleObj.visibility = 'visible';
    }

    this.newPosition += this.dirsign * this.scrollAmount;

    if ((this.dirsign == 1 && this.newPosition > this.stopAt) ||
        (this.dirsign == -1 && this.newPosition < this.stopAt)) {
        if (this.behavior == 'alternate') {
            if (this.onbounce) {
                // fire bounce when alternate changes directions
                this.onbounce();
            }
            this.dirsign = -this.dirsign;
            var temp = this.stopAt;
            this.stopAt = this.startAt;
            this.startAt = temp;
        }
        else {
            // fire start when position is a start
            if (this.onstart) {
                this.onstart();
            }
            this.newPosition = this.startAt;
        }
    }

    switch (this.direction) {
        case 'up':
        case 'down':
            this._setTop(this.newPosition);
            break;

        case 'left':
        case 'right':
        default:
            this._setLeft(this.newPosition);
            break;
    }

    this.runId = setTimeout(this.name + '.start()', this.scrollDelay);
};

XbMarquee.prototype.stop = function () {
    if (this.runId)
        clearTimeout(this.runId);

    this.runId = null;
};

XbMarquee.prototype.setInnerHTML = function (html) {
    if (typeof(this.div.innerHTML) != 'string') {
        return;
    }

    var running = false;
    if (this.runId) {
        running = true;
        this.stop();
    }
    this.html = html;
    this.dirsign = null;
    if (running) {
        this.start();
    }
};

// fixes standards mode in gecko
// since units are required

if (document.layers) {
    XbMarquee.prototype._setLeft = function (left) {
        this.styleObj.left = left;
    };

    XbMarquee.prototype._setTop = function (top) {
        this.styleObj.top = top;
    };
}
else {
    XbMarquee.prototype._setLeft = function (left) {
        this.styleObj.left = left + 'px';
    };

    XbMarquee.prototype._setTop = function (top) {
        this.styleObj.top = top + 'px';
    };
}


