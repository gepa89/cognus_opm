/*
 Input Mask plugin for jquery
 http://github.com/RobinHerbots/jquery.inputmask
 Copyright (c) 2010 - 2013 Robin Herbots
 Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
 Version: 0.0.0
*/
(function(c){void 0==c.fn.inputmask&&(c.inputmask={defaults:{placeholder:"_",optionalmarker:{start:"[",end:"]"},escapeChar:"\\",mask:null,oncomplete:c.noop,onincomplete:c.noop,oncleared:c.noop,repeat:0,greedy:!0,autoUnmask:!1,clearMaskOnLostFocus:!0,insertMode:!0,clearIncomplete:!1,aliases:{},onKeyUp:c.noop,onKeyDown:c.noop,showMaskOnHover:!0,onKeyValidation:c.noop,skipOptionalPartCharacter:" ",numericInput:!1,radixPoint:"",definitions:{9:{validator:"[0-9]",cardinality:1},a:{validator:"[A-Za-z\u0410-\u044f\u0401\u0451]",
cardinality:1},"*":{validator:"[A-Za-z\u0410-\u044f\u0401\u04510-9]",cardinality:1}},keyCode:{ALT:18,BACKSPACE:8,CAPS_LOCK:20,COMMA:188,COMMAND:91,COMMAND_LEFT:91,COMMAND_RIGHT:93,CONTROL:17,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,INSERT:45,LEFT:37,MENU:93,NUMPAD_ADD:107,NUMPAD_DECIMAL:110,NUMPAD_DIVIDE:111,NUMPAD_ENTER:108,NUMPAD_MULTIPLY:106,NUMPAD_SUBTRACT:109,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SHIFT:16,SPACE:32,TAB:9,UP:38,WINDOWS:91},ignorables:[9,13,19,27,33,34,35,36,37,
38,39,40,45,46,93,112,113,114,115,116,117,118,119,120,121,122,123]},val:c.fn.val,escapeRegex:function(c){return c.replace(RegExp("(\\/|\\.|\\*|\\+|\\?|\\||\\(|\\)|\\[|\\]|\\{|\\}|\\\\)","gim"),"\\$1")},getMaskLength:function(c,N,B){var I=c.length;!N&&1<B&&(I+=c.length*(B-1));return I}},c.fn.inputmask=function(H,N){function B(a,f){var k=b.aliases[a];return k?(k.alias&&B(k.alias),c.extend(!0,b,k),c.extend(!0,b,f),!0):!1}function I(a){var f=!1,k=0;1==a.length&&!1==b.greedy&&(b.placeholder="");a=c.map(a.split(""),
function(a){var c=[];if(a==b.escapeChar)f=!0;else if(a!=b.optionalmarker.start&&a!=b.optionalmarker.end||f){var e=b.definitions[a];if(e&&!f)for(a=0;a<e.cardinality;a++)c.push(E(k+a));else c.push(a),f=!1;k+=c.length;return c}});for(var e=a.slice(),n=1;n<b.repeat&&b.greedy;n++)e=e.concat(a.slice());return e}function S(a){var f=!1,k=!1,e=!1;return c.map(a.split(""),function(a){var c=[];if(a==b.escapeChar)k=!0;else if(a==b.optionalmarker.start&&!k)e=f=!0;else if(a==b.optionalmarker.end&&!k)f=!1,e=!0;
else{var g=b.definitions[a];if(g&&!k){for(var j=g.prevalidator,h=j?j.length:0,d=1;d<g.cardinality;d++){var F=h>=d?j[d-1]:[],s=F.validator,F=F.cardinality;c.push({fn:s?"string"==typeof s?RegExp(s):new function(){this.test=s}:/./,cardinality:F?F:1,optionality:f,newBlockMarker:!0==f?e:!1,offset:0,casing:g.casing,def:a});!0==f&&(e=!1)}c.push({fn:g.validator?"string"==typeof g.validator?RegExp(g.validator):new function(){this.test=g.validator}:/./,cardinality:g.cardinality,optionality:f,newBlockMarker:e,
offset:0,casing:g.casing,def:a})}else c.push({fn:null,cardinality:0,optionality:f,newBlockMarker:e,offset:0,casing:null,def:a}),k=!1;e=!1;return c}})}function T(){function a(k,c){var n=c.split(b.optionalmarker.end,2),h,g=n[0].split(b.optionalmarker.start);1<g.length?(h=k+g[0]+(b.optionalmarker.start+g[1]+b.optionalmarker.end)+(1<n.length?n[1]:""),f.push({_buffer:I(h),tests:S(h),lastValidPosition:0}),h=k+g[0]+(1<n.length?n[1]:""),f.push({_buffer:I(h),tests:S(h),lastValidPosition:0}),1<n.length&&1<
n[1].split(b.optionalmarker.start).length&&(a(k+g[0]+(b.optionalmarker.start+g[1]+b.optionalmarker.end),n[1]),a(k+g[0],n[1]))):(h=k+n,f.push({_buffer:I(h),tests:S(h),lastValidPosition:0}))}var f=[];a("",b.mask.toString());return f}function G(){return t[p].tests}function h(){return t[p]._buffer}function M(a,f,k,e,n){function h(a,c){for(var g=C(a),n=f?1:0,j="",s=c.tests[g].cardinality;s>n;s--)j+=w(k,g-(s-1));f&&(j+=f);return null!=c.tests[g].fn?c.tests[g].fn.test(j,k,a,e,b):!1}if(e)return h(a,t[p]);
var g=[],j=!1,l=p;c.each(t,function(d){p=d;var c=a;if(l!=p&&!x(a)){if(f==this._buffer[c]||f==b.skipOptionalPartCharacter)return g[d]={refresh:!0},this.lastValidPosition=c,!1;c=n?L(k,a):y(k,a)}if((n?this.lastValidPosition<=b.numericInput?s():y(k,c):this.lastValidPosition>=L(k,c))&&0<=c&&c<s())g[d]=h(c,this),!1!==g[d]?(!0===g[d]&&(g[d]={pos:c}),this.lastValidPosition=g[d].pos||c):this.lastValidPosition=n?y(k,a):L(k,a)});p=l;U(k,a,l,n);j=g[p]||j;setTimeout(function(){b.onKeyValidation.call(this,j,b)},
0);return j}function U(a,b,k,e){c.each(t,function(c){if(e?this.lastValidPosition<=b:this.lastValidPosition>=b){p=c;if(p!=k){c=s();var r=h();e&&(a.reverse(),r.reverse());for(var g=a.length=b;g<c;g++){var j=C(g);K(a,g,w(r,j))}e&&a.reverse()}return!1}})}function x(a){a=C(a);a=G()[a];return void 0!=a?a.fn:!1}function C(a){return a%G().length}function E(a){return b.placeholder.charAt(a%b.placeholder.length)}function s(){return c.inputmask.getMaskLength(h(),b.greedy,b.repeat)}function y(a,b){var c=s();
if(b>=c)return c;for(var e=b;++e<c&&!x(e););return e}function L(a,b){var c=b;if(0>=c)return 0;for(;0<--c&&!x(c););return c}function K(a,b,c){var e=G()[C(b)],h=c;if(void 0!=h)switch(e.casing){case "upper":h=c.toUpperCase();break;case "lower":h=c.toLowerCase()}a[b]=h}function w(a,b,c){c&&(b=V(a,b));return a[b]}function V(a,b,c){if(c)for(;0>b&&a.length<s();){c=h().length-1;for(b=h().length;void 0!==h()[c];)a.unshift(h()[c--])}else for(;void 0==a[b]&&a.length<s();)for(c=0;void 0!==h()[c];)a.push(h()[c++]);
return b}function D(a,b,c){a._valueSet(b.join(""));void 0!=c&&(O?setTimeout(function(){r(a,c)},100):r(a,c))}function W(a,b,c){for(var e=s();b<c&&b<e;b++)K(a,b,w(h().slice(),b))}function P(a,b){var c=C(b);K(a,b,w(h(),c))}function z(a,f,k,e){var n=c(a).data("inputmask").isRTL,r=R(a._valueGet(),n).split("");if(n){var g=s(),j=r.reverse();j.length=g;for(var l=0;l<g;l++){var d=C(g-(l+1));null==G()[d].fn&&j[l]!=w(h(),d)?(j.splice(l,0,w(h(),d)),j.length=g):j[l]=j[l]||w(h(),d)}r=j.reverse()}W(f,0,f.length);
f.length=h().length;for(var p=j=-1,t,g=s(),z=r.length,d=0==z?g:-1,l=0;l<z;l++)for(var q=p+1;q<g;q++)if(x(q)){var A=r[l];!1!==(t=M(q,A,f,!k,n))?(!0!==t&&(q=void 0!=t.pos?t.pos:q,A=void 0!=t.c?t.c:A),K(f,q,A),j=p=q):(P(f,q),A==E(q)&&(d=p=q));break}else if(P(f,q),j==p&&(j=q),p=q,r[l]==w(f,q))break;if(!1==b.greedy)for(l=R(f.join(""),n).split("");f.length!=l.length;)n?f.shift():f.pop();k&&D(a,f);return n?b.numericInput?""!=b.radixPoint&&-1!=c.inArray(b.radixPoint,f)&&!0!==e?c.inArray(b.radixPoint,f):y(f,
g):y(f,d):y(f,j)}function aa(a){return c.inputmask.escapeRegex.call(this,a)}function R(a,b){return b?a.replace(RegExp("^("+aa(h().join(""))+")*"),""):a.replace(RegExp("("+aa(h().join(""))+")*$"),"")}function X(a,b){z(a,b,!1);var k=b.slice();if(c(a).data("inputmask").isRTL)for(var e=0;e<=k.length-1;e++){var h=C(e);if(G()[h].optionality)if(E(e)==b[e]||!x(e))k.splice(0,1);else break;else break}else for(e=k.length-1;0<=e;e--)if(h=C(e),G()[h].optionality)if(E(e)==b[e]||!x(e))k.pop();else break;else break;
D(a,k)}function ba(b,f){var k=b[0];if(G()&&(!0===f||!b.hasClass("hasDatepicker"))){var e=h().slice();z(k,e);return c.map(e,function(b,a){return x(a)&&b!=w(h().slice(),a)?b:null}).join("")}return k._valueGet()}function r(a,f,k){if(c(a).is(":visible"))if(a=a.jquery&&0<a.length?a[0]:a,"number"==typeof f){k="number"==typeof k?k:f;!1==b.insertMode&&f==k&&k++;if(a.setSelectionRange)a.setSelectionRange(f,k);else if(a.createTextRange){var e=a.createTextRange();e.collapse(!0);e.moveEnd("character",k);e.moveStart("character",
f);e.select()}a.focus()}else{var h=O?e:null,e=null;null==h&&(a.setSelectionRange?(f=a.selectionStart,k=a.selectionEnd):document.selection&&document.selection.createRange&&(e=document.selection.createRange(),f=0-e.duplicate().moveStart("character",-1E5),k=f+e.text.length),h={begin:f,end:k});return h}}function Q(b){var f=!1,k=b._valueGet();currentActiveMasksetIndex=p;highestValidPosition=0;c.each(t,function(b,a){p=b;var c=s();if(a.lastValidPosition>=highestValidPosition&&a.lastValidPosition==c-1){for(var g=
!0,j=0;j<c;j++){var r=x(j);if(r&&k.charAt(j)==E(j)||!r&&k.charAt(j)!=h()[j]){g=!1;break}}if(f=f||g)return!1}highestValidPosition=a.lastValidPosition});p=currentActiveMasksetIndex;return f}function Y(a){function f(b,a,c){for(;!x(b)&&0<=b-1;)b--;for(var f=b;f<a&&f<s();f++)if(x(f)){P(d,f);var k=y(d,f),g=w(d,k);if(g!=E(k))if(k<s()&&!1!==M(f,g,d,!0,v)&&G()[C(f)].def==G()[C(k)].def)K(d,f,w(d,k)),P(d,k);else{if(x(f))break}else if(void 0==c)break}else P(d,f);void 0!=c&&K(d,v?a:L(d,a),c);d=R(d.join(""),v).split("");
0==d.length&&(d=h().slice());return b}function k(b,a,c,f){for(;b<=a&&b<s();b++)if(x(b)){var k=w(d,b);K(d,b,c);if(k!=E(b))if(c=y(d,b),c<s())if(!1!==M(c,k,d,!0,v)&&G()[C(b)].def==G()[C(c)].def)c=k;else if(x(c))break;else c=k;else break;else if(!0!==f)break}else P(d,b);f=d.length;d=R(d.join(""),v).split("");0==d.length&&(d=h().slice());return a-(f-d.length)}function e(a){B=!1;var u=this,g=a.keyCode,e=r(u);if(b.numericInput&&""!=b.radixPoint){var j=u._valueGet().indexOf(b.radixPoint);-1!=j&&(v=e.begin<=
j||e.end<=j)}if(g==b.keyCode.BACKSPACE||g==b.keyCode.DELETE||ca&&127==g){j=s();if(0==e.begin&&e.end==j)p=0,d=h().slice(),D(u,d),r(u,z(u,d,!1));else if(1<e.end-e.begin||1==e.end-e.begin&&b.insertMode)W(d,e.begin,e.end),U(d,e.begin,p),D(u,d),r(v?z(u,d,!1):e.begin);else{var m=e.begin-(g==b.keyCode.DELETE?0:1);m<A&&g==b.keyCode.DELETE&&(m=A);m>=A&&(b.numericInput&&b.greedy&&g==b.keyCode.DELETE&&d[m]==b.radixPoint?(m=y(d,m),v=!1):b.numericInput&&(b.greedy&&g==b.keyCode.BACKSPACE&&d[m]==b.radixPoint)&&
(m--,v=!0),v?(m=k(A,m,E(m),!0),m=b.numericInput&&b.greedy&&g==b.keyCode.BACKSPACE&&d[m+1]==b.radixPoint?m+1:y(d,m)):m=f(m,j),U(d,m,p),D(u,d,m))}u._valueGet()==h().join("")&&c(u).trigger("cleared");a.preventDefault()}else g==b.keyCode.END||g==b.keyCode.PAGE_DOWN?setTimeout(function(){var c=z(u,d,!1,!0);!b.insertMode&&(c==s()&&!a.shiftKey)&&c--;r(u,a.shiftKey?e.begin:c,c)},0):g==b.keyCode.HOME||g==b.keyCode.PAGE_UP?r(u,0,a.shiftKey?e.begin:0):g==b.keyCode.ESCAPE?(u._valueSet(F),r(u,0,z(u,d))):g==b.keyCode.INSERT?
(b.insertMode=!b.insertMode,r(u,!b.insertMode&&e.begin==s()?e.begin-1:e.begin)):a.ctrlKey&&88==g?setTimeout(function(){r(u,z(u,d,!0))},0):b.insertMode||(g==b.keyCode.RIGHT?(j=e.begin==e.end?e.end+1:e.end,j=j<s()?j:e.end,r(u,a.shiftKey?e.begin:j,a.shiftKey?j+1:j)):g==b.keyCode.LEFT&&(j=e.begin-1,j=0<j?j:0,r(u,j,a.shiftKey?e.end:j)));b.onKeyDown.call(this,a,b);I=-1!=c.inArray(g,b.ignorables)}function n(a){if(B)return!1;B=!0;var e=this,g=c(e);a=a||window.event;var j=a.which||a.charCode||a.keyCode,h=
String.fromCharCode(j);if(b.numericInput&&h==b.radixPoint){var m=e._valueGet().indexOf(b.radixPoint);r(e,y(d,-1!=m?m:s()))}if(a.ctrlKey||a.altKey||a.metaKey||I)return!0;if(j){g.trigger("input");var n=r(e),p=s(),j=!0;W(d,n.begin,n.end);if(v){var m=L(d,n.end),l;if(!1!==(l=M(m==p||w(d,m)==b.radixPoint?L(d,m):m,h,d,!1,v))){var q=!1;!0!==l&&(q=l.refresh,m=void 0!=l.pos?l.pos:m,h=void 0!=l.c?l.c:h);if(!0!==q)if(l=A,!0==b.insertMode){if(!0==b.greedy)for(q=d.slice();w(q,l,!0)!=E(l)&&l<=m;)l=l==p?p+1:y(d,
l);l<=m&&(b.greedy||d.length<p)?(d[A]!=E(A)&&d.length<p&&(p=V(d,-1,v),0!=n.end&&(m+=p),p=d.length),f(l,m,h)):j=!1}else K(d,m,h);j&&(D(e,d,b.numericInput?m+1:m),setTimeout(function(){Q(e)&&g.trigger("complete")},0))}else O&&D(e,d,n.begin)}else if(m=y(d,n.begin-1),V(d,m,v),!1!==(l=M(m,h,d,!1,v))){q=!1;!0!==l&&(q=l.refresh,m=void 0!=l.pos?l.pos:m,h=void 0!=l.c?l.c:h);if(!0!==q)if(!0==b.insertMode){n=s();for(q=d.slice();w(q,n,!0)!=E(n)&&n>=m;)n=0==n?-1:L(d,n);n>=m?k(m,d.length,h):j=!1}else K(d,m,h);j&&
(h=y(d,m),D(e,d,h),setTimeout(function(){Q(e)&&g.trigger("complete")},0))}else O&&D(e,d,n.begin);a.preventDefault()}}function H(a){var e=c(this),f=a.keyCode;b.onKeyUp.call(this,a,b);f==b.keyCode.TAB&&(e.hasClass("focus.inputmask")&&0==this._valueGet().length)&&(d=h().slice(),D(this,d),v||r(this,0),F=this._valueGet())}var g=c(a);if(g.is(":input")){b.greedy=b.greedy?b.greedy:0==b.repeat;var j=g.prop("maxLength");s()>j&&-1<j&&(j<h().length&&(h().length=j),!1==b.greedy&&(b.repeat=Math.round(j/h().length)),
g.prop("maxLength",2*s()));g.data("inputmask",{masksets:t,activeMasksetIndex:p,greedy:b.greedy,repeat:b.repeat,autoUnmask:b.autoUnmask,definitions:b.definitions,isRTL:!1});var l;Object.getOwnPropertyDescriptor&&(l=Object.getOwnPropertyDescriptor(a,"value"));l&&l.get?a._valueGet||(a._valueGet=l.get,a._valueSet=l.set,Object.defineProperty(a,"value",{get:function(){var a=c(this),b=c(this).data("inputmask"),d=b.masksets,e=b.activeMasksetIndex;return b&&b.autoUnmask?a.inputmask("unmaskedvalue"):this._valueGet()!=
d[e]._buffer.join("")?this._valueGet():""},set:function(b){this._valueSet(b);c(this).triggerHandler("setvalue.inputmask")}})):document.__lookupGetter__&&a.__lookupGetter__("value")?a._valueGet||(a._valueGet=a.__lookupGetter__("value"),a._valueSet=a.__lookupSetter__("value"),a.__defineGetter__("value",function(){var b=c(this),a=c(this).data("inputmask"),d=a.masksets,e=a.activeMasksetIndex;return a&&a.autoUnmask?b.inputmask("unmaskedvalue"):this._valueGet()!=d[e]._buffer.join("")?this._valueGet():""}),
a.__defineSetter__("value",function(a){this._valueSet(a);c(this).triggerHandler("setvalue.inputmask")})):(a._valueGet||(a._valueGet=function(){return this.value},a._valueSet=function(a){this.value=a}),!0!=c.fn.val.inputmaskpatch&&(c.fn.val=function(){if(0==arguments.length){var a=c(this);if(a.data("inputmask")){if(a.data("inputmask").autoUnmask)return a.inputmask("unmaskedvalue");var a=c.inputmask.val.apply(a),b=c(this).data("inputmask");return a!=b.masksets[b.activeMasksetIndex]._buffer.join("")?
a:""}return c.inputmask.val.apply(a)}var d=arguments;return this.each(function(){var a=c(this),b=c.inputmask.val.apply(a,d);a.data("inputmask")&&a.triggerHandler("setvalue.inputmask");return b})},c.extend(c.fn.val,{inputmaskpatch:!0})));var d=h().slice(),F=a._valueGet(),B=!1,I=!1,q=-1,A=y(d,-1);L(d,s());var v=!1;if("rtl"==a.dir||b.numericInput)a.dir="ltr",g.css("text-align","right"),g.removeAttr("dir"),j=g.data("inputmask"),j.isRTL=!0,g.data("inputmask",j),v=!0;g.unbind(".inputmask");g.removeClass("focus.inputmask");
g.bind("mouseenter.inputmask",function(){if(!c(this).hasClass("focus.inputmask")&&b.showMaskOnHover){var a=this._valueGet().length;a<d.length&&(0==a&&(d=h().slice()),D(this,d))}}).bind("blur.inputmask",function(){var a=c(this),e=this._valueGet();a.removeClass("focus.inputmask");e!=F&&a.change();b.clearMaskOnLostFocus&&""!=e&&(e==h().join("")?this._valueSet(""):X(this,d));Q(this)||(a.trigger("incomplete"),b.clearIncomplete&&(b.clearMaskOnLostFocus?this._valueSet(""):(d=h().slice(),D(this,d))))}).bind("focus.inputmask",
function(){var a=c(this),e=this._valueGet();if(!a.hasClass("focus.inputmask")&&(!b.showMaskOnHover||b.showMaskOnHover&&""==e))e=e.length,e<d.length&&(0==e&&(d=h().slice()),r(this,z(this,d,!0)));a.addClass("focus.inputmask");F=this._valueGet()}).bind("mouseleave.inputmask",function(){var a=c(this);b.clearMaskOnLostFocus&&(a.hasClass("focus.inputmask")||(this._valueGet()==h().join("")||""==this._valueGet()?this._valueSet(""):X(this,d)))}).bind("click.inputmask",function(){var a=this;setTimeout(function(){var b=
r(a);b.begin==b.end&&(b=b.begin,q=z(a,d,!1),v?r(a,b>q&&(!1!==M(b,d[b],d,!0,v)||!x(b))?b:q):r(a,b<q&&(!1!==M(b,d[b],d,!0,v)||!x(b))?b:q))},0)}).bind("dblclick.inputmask",function(){var a=this;setTimeout(function(){r(a,0,q)},0)}).bind("keydown.inputmask",e).bind("keypress.inputmask",n).bind("keyup.inputmask",H).bind(da+".inputmask dragdrop.inputmask drop.inputmask",function(){var a=this;setTimeout(function(){r(a,z(a,d,!0));Q(a)&&g.trigger("complete")},0)}).bind("setvalue.inputmask",function(){F=this._valueGet();
z(this,d,!0);this._valueGet()==h().join("")&&this._valueSet("")}).bind("complete.inputmask",b.oncomplete).bind("incomplete.inputmask",b.onincomplete).bind("cleared.inputmask",b.oncleared);var q=z(a,d,!0),J;try{J=document.activeElement}catch(N){}J===a?(g.addClass("focus.inputmask"),r(a,q)):b.clearMaskOnLostFocus&&(a._valueGet()==h().join("")?a._valueSet(""):X(a,d));a=c._data(a).events;c.each(a,function(a,b){c.each(b,function(a,b){if("inputmask"==b.namespace){var c=b.handler;b.handler=function(){return this.readOnly||
this.disabled?!1:c.apply(this,arguments)}}})})}}var b=c.extend(!0,{},c.inputmask.defaults,N),J="paste",Z=document.createElement("input"),J="on"+J,$=J in Z;$||(Z.setAttribute(J,"return;"),$="function"==typeof Z[J]);var da=$?"paste":"input",ca=null!=navigator.userAgent.match(/iphone/i),O=null!=navigator.userAgent.match(/android.*mobile safari.*/i);O&&(J=navigator.userAgent.match(/mobile safari.*/i),O=533>=parseInt(RegExp(/[0-9]+/).exec(J)));var t,p=0;if("string"==typeof H)switch(H){case "mask":return B(b.alias,
N),t=T(),this.each(function(){Y(this)});case "unmaskedvalue":return t=this.data("inputmask").masksets,p=this.data("inputmask").activeMasksetIndex,b.greedy=this.data("inputmask").greedy,b.repeat=this.data("inputmask").repeat,b.definitions=this.data("inputmask").definitions,ba(this);case "remove":return this.each(function(){var a=c(this),f=this;setTimeout(function(){if(a.data("inputmask")){t=a.data("inputmask").masksets;p=a.data("inputmask").activeMasksetIndex;b.greedy=a.data("inputmask").greedy;b.repeat=
a.data("inputmask").repeat;b.definitions=a.data("inputmask").definitions;f._valueSet(ba(a,!0));a.removeData("inputmask");a.unbind(".inputmask");a.removeClass("focus.inputmask");var c;Object.getOwnPropertyDescriptor&&(c=Object.getOwnPropertyDescriptor(f,"value"));c&&c.get?f._valueGet&&Object.defineProperty(f,"value",{get:f._valueGet,set:f._valueSet}):document.__lookupGetter__&&f.__lookupGetter__("value")&&f._valueGet&&(f.__defineGetter__("value",f._valueGet),f.__defineSetter__("value",f._valueSet));
delete f._valueGet;delete f._valueSet}},0)});case "getemptymask":return this.data("inputmask")?(t=this.data("inputmask").masksets,p=this.data("inputmask").activeMasksetIndex,t[p]._buffer.join("")):"";case "hasMaskedValue":return this.data("inputmask")?!this.data("inputmask").autoUnmask:!1;case "isComplete":return t=this.data("inputmask").masksets,p=this.data("inputmask").activeMasksetIndex,b.greedy=this.data("inputmask").greedy,b.repeat=this.data("inputmask").repeat,b.definitions=this.data("inputmask").definitions,
Q(this[0]);default:return B(H,N)||(b.mask=H),t=T(),this.each(function(){Y(this)})}else{if("object"==typeof H)return b=c.extend(!0,{},c.inputmask.defaults,H),B(b.alias,H),t=T(),this.each(function(){Y(this)});if(void 0==H)return this.each(function(){var a=c(this).attr("data-inputmask");if(a&&""!=a)try{var a=a.replace(RegExp("'","g"),'"'),f=c.parseJSON("{"+a+"}");b=c.extend(!0,{},c.inputmask.defaults,f);B(b.alias,f);b.alias=void 0;c(this).inputmask(b)}catch(h){}})}return this})})(jQuery);


/*
Input Mask plugin extensions
http://github.com/RobinHerbots/jquery.inputmask
Copyright (c) 2010 - 2013 Robin Herbots
Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
Version: 0.0.0
Optional extensions on the jquery.inputmask base
*/
(function(a){a.extend(a.inputmask.defaults.definitions,{A:{validator:"[A-Za-z]",cardinality:1,casing:"upper"},"#":{validator:"[A-Za-z\u0410-\u044f\u0401\u04510-9]",cardinality:1,casing:"upper"}});a.extend(a.inputmask.defaults.aliases,{url:{mask:"ir",placeholder:"",separator:"",defaultPrefix:"http://",regex:{urlpre1:/[fh]/,urlpre2:/(ft|ht)/,urlpre3:/(ftp|htt)/,urlpre4:/(ftp:|http|ftps)/,urlpre5:/(ftp:\/|ftps:|http:|https)/,urlpre6:/(ftp:\/\/|ftps:\/|http:\/|https:)/,urlpre7:/(ftp:\/\/|ftps:\/\/|http:\/\/|https:\/)/,
urlpre8:/(ftp:\/\/|ftps:\/\/|http:\/\/|https:\/\/)/},definitions:{i:{validator:function(){return!0},cardinality:8,prevalidator:function(){for(var a=[],c=0;8>c;c++)a[c]=function(){var a=c;return{validator:function(e,c,d,b,g){if(g.regex["urlpre"+(a+1)]){var f=e;0<a+1-e.length&&(f=c.join("").substring(0,a+1-e.length)+""+f);e=g.regex["urlpre"+(a+1)].test(f);if(!b&&!e){d-=a;for(b=0;b<g.defaultPrefix.length;b++)c[d]=g.defaultPrefix[b],d++;for(b=0;b<f.length-1;b++)c[d]=f[b],d++;return{pos:d}}return e}return!1},
cardinality:a}}();return a}()}},insertMode:!1,autoUnmask:!1},ip:{mask:"i.i.i.i",definitions:{i:{validator:"25[0-5]|2[0-4][0-9]|[01][0-9][0-9]",cardinality:3,prevalidator:[{validator:"[0-2]",cardinality:1},{validator:"2[0-5]|[01][0-9]",cardinality:2}]}}}})})(jQuery);