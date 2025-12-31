<?php
/**
 * Simple URL Shortener : Cut It Off
 * Features:
 * - API with rate limiting & token authentication
 * - Custom short codes (optional)
 * - Title/description metadata
 * - Overwrite protection (configurable)
 * - Admin panel for management
 * - QR code generation
 */

define('DATA_FILE', __DIR__ . '/urls.json');
define('CONFIG_FILE', __DIR__ . '/config.json');
define('RATE_LIMITS_FILE', __DIR__ . '/rate_limits.json');
define('BASE_URL', ''); // Leave empty to auto-detect

$version = '1.0.8';
$versiondate = '31/12/2025';

$QRcode_library = 'var qrcode=function(){var t=function(t,r){var e=t,n=g[r],o=null,i=0,a=null,u=[],f={},c=function(t,r){o=function(t){for(var r=new Array(t),e=0;e<t;e+=1){r[e]=new Array(t);for(var n=0;n<t;n+=1)r[e][n]=null}return r}(i=4*e+17),l(0,0),l(i-7,0),l(0,i-7),s(),h(),d(t,r),e>=7&&v(t),null==a&&(a=p(e,n,u)),w(a,r)},l=function(t,r){for(var e=-1;e<=7;e+=1)if(!(t+e<=-1||i<=t+e))for(var n=-1;n<=7;n+=1)r+n<=-1||i<=r+n||(o[t+e][r+n]=0<=e&&e<=6&&(0==n||6==n)||0<=n&&n<=6&&(0==e||6==e)||2<=e&&e<=4&&2<=n&&n<=4)},h=function(){for(var t=8;t<i-8;t+=1)null==o[t][6]&&(o[t][6]=t%2==0);for(var r=8;r<i-8;r+=1)null==o[6][r]&&(o[6][r]=r%2==0)},s=function(){for(var t=B.getPatternPosition(e),r=0;r<t.length;r+=1)for(var n=0;n<t.length;n+=1){var i=t[r],a=t[n];if(null==o[i][a])for(var u=-2;u<=2;u+=1)for(var f=-2;f<=2;f+=1)o[i+u][a+f]=-2==u||2==u||-2==f||2==f||0==u&&0==f}},v=function(t){for(var r=B.getBCHTypeNumber(e),n=0;n<18;n+=1){var a=!t&&1==(r>>n&1);o[Math.floor(n/3)][n%3+i-8-3]=a}for(n=0;n<18;n+=1){a=!t&&1==(r>>n&1);o[n%3+i-8-3][Math.floor(n/3)]=a}},d=function(t,r){for(var e=n<<3|r,a=B.getBCHTypeInfo(e),u=0;u<15;u+=1){var f=!t&&1==(a>>u&1);u<6?o[u][8]=f:u<8?o[u+1][8]=f:o[i-15+u][8]=f}for(u=0;u<15;u+=1){f=!t&&1==(a>>u&1);u<8?o[8][i-u-1]=f:u<9?o[8][15-u-1+1]=f:o[8][15-u-1]=f}o[i-8][8]=!t},w=function(t,r){for(var e=-1,n=i-1,a=7,u=0,f=B.getMaskFunction(r),c=i-1;c>0;c-=2)for(6==c&&(c-=1);;){for(var g=0;g<2;g+=1)if(null==o[n][c-g]){var l=!1;u<t.length&&(l=1==(t[u]>>>a&1)),f(n,c-g)&&(l=!l),o[n][c-g]=l,-1==(a-=1)&&(u+=1,a=7)}if((n+=e)<0||i<=n){n-=e,e=-e;break}}},p=function(t,r,e){for(var n=A.getRSBlocks(t,r),o=b(),i=0;i<e.length;i+=1){var a=e[i];o.put(a.getMode(),4),o.put(a.getLength(),B.getLengthInBits(a.getMode(),t)),a.write(o)}var u=0;for(i=0;i<n.length;i+=1)u+=n[i].dataCount;if(o.getLengthInBits()>8*u)throw"code length overflow. ("+o.getLengthInBits()+">"+8*u+")";for(o.getLengthInBits()+4<=8*u&&o.put(0,4);o.getLengthInBits()%8!=0;)o.putBit(!1);for(;!(o.getLengthInBits()>=8*u||(o.put(236,8),o.getLengthInBits()>=8*u));)o.put(17,8);return function(t,r){for(var e=0,n=0,o=0,i=new Array(r.length),a=new Array(r.length),u=0;u<r.length;u+=1){var f=r[u].dataCount,c=r[u].totalCount-f;n=Math.max(n,f),o=Math.max(o,c),i[u]=new Array(f);for(var g=0;g<i[u].length;g+=1)i[u][g]=255&t.getBuffer()[g+e];e+=f;var l=B.getErrorCorrectPolynomial(c),h=k(i[u],l.getLength()-1).mod(l);for(a[u]=new Array(l.getLength()-1),g=0;g<a[u].length;g+=1){var s=g+h.getLength()-a[u].length;a[u][g]=s>=0?h.getAt(s):0}}var v=0;for(g=0;g<r.length;g+=1)v+=r[g].totalCount;var d=new Array(v),w=0;for(g=0;g<n;g+=1)for(u=0;u<r.length;u+=1)g<i[u].length&&(d[w]=i[u][g],w+=1);for(g=0;g<o;g+=1)for(u=0;u<r.length;u+=1)g<a[u].length&&(d[w]=a[u][g],w+=1);return d}(o,n)};f.addData=function(t,r){var e=null;switch(r=r||"Byte"){case"Numeric":e=M(t);break;case"Alphanumeric":e=x(t);break;case"Byte":e=m(t);break;case"Kanji":e=L(t);break;default:throw"mode:"+r}u.push(e),a=null},f.isDark=function(t,r){if(t<0||i<=t||r<0||i<=r)throw t+","+r;return o[t][r]},f.getModuleCount=function(){return i},f.make=function(){if(e<1){for(var t=1;t<40;t++){for(var r=A.getRSBlocks(t,n),o=b(),i=0;i<u.length;i++){var a=u[i];o.put(a.getMode(),4),o.put(a.getLength(),B.getLengthInBits(a.getMode(),t)),a.write(o)}var g=0;for(i=0;i<r.length;i++)g+=r[i].dataCount;if(o.getLengthInBits()<=8*g)break}e=t}c(!1,function(){for(var t=0,r=0,e=0;e<8;e+=1){c(!0,e);var n=B.getLostPoint(f);(0==e||t>n)&&(t=n,r=e)}return r}())},f.createSvgTag=function(t,r,e,n){var o={};"object"==typeof arguments[0]&&(t=(o=arguments[0]).cellSize,r=o.margin,e=o.alt,n=o.title),t=t||2,r=void 0===r?4*t:r;var i,a,u,c,g=f.getModuleCount()*t+2*r,l="";for(c="l"+t+",0 0,"+t+" -"+t+",0 0,-"+t+"z ",l+=\'<svg version="1.1" xmlns="http://www.w3.org/2000/svg"\',l+=o.scalable?"":" width=\""+g+"px\" height=\""+g+"px\"",l+=" viewBox=\"0 0 "+g+" "+g+"\" ",l+=\' preserveAspectRatio="xMinYMin meet"\',l+=">",l+=\'<rect width="100%" height="100%" fill="white" cx="0" cy="0"/>\',l+=\'<path d="\',a=0;a<f.getModuleCount();a+=1)for(u=a*t+r,i=0;i<f.getModuleCount();i+=1)f.isDark(a,i)&&(l+="M"+(i*t+r)+","+u+c);return l+=\'" stroke="transparent" fill="black"/>\',l+="</svg>"};return f};var r,e,n,o,i,a=1,u=2,f=4,c=8,g={L:1,M:0,Q:3,H:2},l=0,h=1,s=2,v=3,d=4,w=5,p=6,y=7,B=(r=[[],[6,18],[6,22],[6,26],[6,30],[6,34],[6,22,38],[6,24,42],[6,26,46],[6,28,50],[6,30,54],[6,32,58],[6,34,62],[6,26,46,66],[6,26,48,70],[6,26,50,74],[6,30,54,78],[6,30,56,82],[6,30,58,86],[6,34,62,90],[6,28,50,72,94],[6,26,50,74,98],[6,30,54,78,102],[6,28,54,80,106],[6,32,58,84,110],[6,30,58,86,114],[6,34,62,90,118],[6,26,50,74,98,122],[6,30,54,78,102,126],[6,26,52,78,104,130],[6,30,56,82,108,134],[6,34,60,86,112,138],[6,30,58,86,114,142],[6,34,62,90,118,146],[6,30,54,78,102,126,150],[6,24,50,76,102,128,154],[6,28,54,80,106,132,158],[6,32,58,84,110,136,162],[6,26,54,82,110,138,166],[6,30,58,86,114,142,170]],e=1335,n=7973,i=function(t){for(var r=0;0!=t;)r+=1,t>>>=1;return r},(o={}).getBCHTypeInfo=function(t){for(var r=t<<10;i(r)-i(e)>=0;)r^=e<<i(r)-i(e);return 21522^(t<<10|r)},o.getBCHTypeNumber=function(t){for(var r=t<<12;i(r)-i(n)>=0;)r^=n<<i(r)-i(n);return t<<12|r},o.getPatternPosition=function(t){return r[t-1]},o.getMaskFunction=function(t){switch(t){case l:return function(t,r){return(t+r)%2==0};case h:return function(t,r){return t%2==0};case s:return function(t,r){return r%3==0};case v:return function(t,r){return(t+r)%3==0};case d:return function(t,r){return(Math.floor(t/2)+Math.floor(r/3))%2==0};case w:return function(t,r){return t*r%2+t*r%3==0};case p:return function(t,r){return(t*r%2+t*r%3)%2==0};case y:return function(t,r){return(t*r%3+(t+r)%2)%2==0};default:throw"bad maskPattern:"+t}},o.getErrorCorrectPolynomial=function(t){for(var r=k([1],0),e=0;e<t;e+=1)r=r.multiply(k([1,C.gexp(e)],0));return r},o.getLengthInBits=function(t,r){if(1<=r&&r<10)switch(t){case a:return 10;case u:return 9;case f:case c:return 8;default:throw"mode:"+t}else if(r<27)switch(t){case a:return 12;case u:return 11;case f:return 16;case c:return 10;default:throw"mode:"+t}else{if(!(r<41))throw"type:"+r;switch(t){case a:return 14;case u:return 13;case f:return 16;case c:return 12;default:throw"mode:"+t}}},o.getLostPoint=function(t){for(var r=t.getModuleCount(),e=0,n=0;n<r;n+=1)for(var o=0;o<r;o+=1){for(var i=0,a=t.isDark(n,o),u=-1;u<=1;u+=1)if(!(n+u<0||r<=n+u))for(var f=-1;f<=1;f+=1)o+f<0||r<=o+f||0==u&&0==f||a==t.isDark(n+u,o+f)&&(i+=1);i>5&&(e+=3+i-5)}for(n=0;n<r-1;n+=1)for(o=0;o<r-1;o+=1){var c=0;t.isDark(n,o)&&(c+=1),t.isDark(n+1,o)&&(c+=1),t.isDark(n,o+1)&&(c+=1),t.isDark(n+1,o+1)&&(c+=1),0!=c&&4!=c||(e+=3)}for(n=0;n<r;n+=1)for(o=0;o<r-6;o+=1)t.isDark(n,o)&&!t.isDark(n,o+1)&&t.isDark(n,o+2)&&t.isDark(n,o+3)&&t.isDark(n,o+4)&&!t.isDark(n,o+5)&&t.isDark(n,o+6)&&(e+=40);for(o=0;o<r;o+=1)for(n=0;n<r-6;n+=1)t.isDark(n,o)&&!t.isDark(n+1,o)&&t.isDark(n+2,o)&&t.isDark(n+3,o)&&t.isDark(n+4,o)&&!t.isDark(n+5,o)&&t.isDark(n+6,o)&&(e+=40);var g=0;for(o=0;o<r;o+=1)for(n=0;n<r;n+=1)t.isDark(n,o)&&(g+=1);return e+=Math.abs(100*g/r/r-50)/5*10},o),C=function(){for(var t=new Array(256),r=new Array(256),e=0;e<8;e+=1)t[e]=1<<e;for(e=8;e<256;e+=1)t[e]=t[e-4]^t[e-5]^t[e-6]^t[e-8];for(e=0;e<255;e+=1)r[t[e]]=e;var n={glog:function(t){if(t<1)throw"glog("+t+")";return r[t]},gexp:function(r){for(;r<0;)r+=255;for(;r>=256;)r-=255;return t[r]}};return n}();function k(t,r){if(void 0===t.length)throw t.length+"/"+r;var e=function(){for(var e=0;e<t.length&&0==t[e];)e+=1;for(var n=new Array(t.length-e+r),o=0;o<t.length-e;o+=1)n[o]=t[o+e];return n}(),n={getAt:function(t){return e[t]},getLength:function(){return e.length},multiply:function(t){for(var r=new Array(n.getLength()+t.getLength()-1),e=0;e<n.getLength();e+=1)for(var o=0;o<t.getLength();o+=1)r[e+o]^=C.gexp(C.glog(n.getAt(e))+C.glog(t.getAt(o)));return k(r,0)},mod:function(t){if(n.getLength()-t.getLength()<0)return n;for(var r=C.glog(n.getAt(0))-C.glog(t.getAt(0)),e=new Array(n.getLength()),o=0;o<n.getLength();o+=1)e[o]=n.getAt(o);for(o=0;o<t.getLength();o+=1)e[o]^=C.gexp(C.glog(t.getAt(o))+r);return k(e,0).mod(t)}};return n}var A=function(){var t=[[1,26,19],[1,26,16],[1,26,13],[1,26,9],[1,44,34],[1,44,28],[1,44,22],[1,44,16],[1,70,55],[1,70,44],[2,35,17],[2,35,13],[1,100,80],[2,50,32],[2,50,24],[4,25,9],[1,134,108],[2,67,43],[2,33,15,2,34,16],[2,33,11,2,34,12],[2,86,68],[4,43,27],[4,43,19],[4,43,15],[2,98,78],[4,49,31],[2,32,14,4,33,15],[4,39,13,1,40,14],[2,121,97],[2,60,38,2,61,39],[4,40,18,2,41,19],[4,40,14,2,41,15],[2,146,116],[3,58,36,2,59,37],[4,36,16,4,37,17],[4,36,12,4,37,13],[2,86,68,2,87,69],[4,69,43,1,70,44],[6,43,19,2,44,20],[6,43,15,2,44,16],[4,101,81],[1,80,50,4,81,51],[4,50,22,4,51,23],[3,36,12,8,37,13],[2,116,92,2,117,93],[6,58,36,2,59,37],[4,46,20,6,47,21],[7,42,14,4,43,15],[4,133,107],[8,59,37,1,60,38],[8,44,20,4,45,21],[12,33,11,4,34,12],[3,145,115,1,146,116],[4,64,40,5,65,41],[11,36,16,5,37,17],[11,36,12,5,37,13],[5,109,87,1,110,88],[5,65,41,5,66,42],[5,54,24,7,55,25],[11,36,12,7,37,13],[5,122,98,1,123,99],[7,73,45,3,74,46],[15,43,19,2,44,20],[3,45,15,13,46,16],[1,135,107,5,136,108],[10,74,46,1,75,47],[1,50,22,15,51,23],[2,42,14,17,43,15],[5,150,120,1,151,121],[9,69,43,4,70,44],[17,50,22,1,51,23],[2,42,14,19,43,15],[3,141,113,4,142,114],[3,70,44,11,71,45],[17,47,21,4,48,22],[9,39,13,16,40,14],[3,135,107,5,136,108],[3,67,41,13,68,42],[15,54,24,5,55,25],[15,43,15,10,44,16],[4,144,116,4,145,117],[17,68,42],[17,50,22,6,51,23],[19,46,16,6,47,17],[2,139,111,7,140,112],[17,74,46],[7,54,24,16,55,25],[34,37,13],[4,151,121,5,152,122],[4,75,47,14,76,48],[11,54,24,14,55,25],[16,45,15,14,46,16],[6,147,117,4,148,118],[6,73,45,14,74,46],[11,54,24,16,55,25],[30,46,16,2,47,17],[8,132,106,4,133,107],[8,75,47,13,76,48],[7,54,24,22,55,25],[22,45,15,13,46,16],[10,142,114,2,143,115],[19,74,46,4,75,47],[28,50,22,6,51,23],[33,46,16,4,47,17],[8,152,122,4,153,123],[22,73,45,3,74,46],[8,53,23,26,54,24],[12,45,15,28,46,16],[3,147,117,10,148,118],[3,73,45,23,74,46],[4,54,24,31,55,25],[11,45,15,31,46,16],[7,146,116,7,147,117],[21,73,45,7,74,46],[1,53,23,37,54,24],[19,45,15,26,46,16],[5,145,115,10,146,116],[19,75,47,10,76,48],[15,54,24,25,55,25],[23,45,15,25,46,16],[13,145,115,3,146,116],[2,74,46,29,75,47],[42,54,24,1,55,25],[23,45,15,28,46,16],[17,145,115],[10,74,46,23,75,47],[10,54,24,35,55,25],[19,45,15,35,46,16],[17,145,115,1,146,116],[14,74,46,21,75,47],[29,54,24,19,55,25],[11,45,15,46,46,16],[13,145,115,6,146,116],[14,74,46,23,75,47],[44,54,24,7,55,25],[59,46,16,1,47,17],[12,151,121,7,152,122],[12,75,47,26,76,48],[39,54,24,14,55,25],[22,45,15,41,46,16],[6,151,121,14,152,122],[6,75,47,34,76,48],[46,54,24,10,55,25],[2,45,15,64,46,16],[17,152,122,4,153,123],[29,74,46,14,75,47],[49,54,24,10,55,25],[24,45,15,46,46,16],[4,152,122,18,153,123],[13,74,46,32,75,47],[48,54,24,14,55,25],[42,45,15,32,46,16],[20,147,117,4,148,118],[40,75,47,7,76,48],[43,54,24,22,55,25],[10,45,15,67,46,16],[19,148,118,6,149,119],[18,75,47,31,76,48],[34,54,24,34,55,25],[20,45,15,61,46,16]],r=function(t,r){var e={};return e.totalCount=t,e.dataCount=r,e},e={};return e.getRSBlocks=function(e,n){var o=function(r,e){switch(e){case g.L:return t[4*(r-1)+0];case g.M:return t[4*(r-1)+1];case g.Q:return t[4*(r-1)+2];case g.H:return t[4*(r-1)+3];default:return}}(e,n);if(void 0===o)throw"bad rs block @ typeNumber:"+e+"/errorCorrectionLevel:"+n;for(var i=o.length/3,a=[],u=0;u<i;u+=1)for(var f=o[3*u+0],c=o[3*u+1],l=o[3*u+2],h=0;h<f;h+=1)a.push(r(c,l));return a},e}(),b=function(){var t=[],r=0,e={getBuffer:function(){return t},getAt:function(r){var e=Math.floor(r/8);return 1==(t[e]>>>7-r%8&1)},put:function(t,r){for(var n=0;n<r;n+=1)e.putBit(1==(t>>>r-n-1&1))},getLengthInBits:function(){return r},putBit:function(e){var n=Math.floor(r/8);t.length<=n&&t.push(0),e&&(t[n]|=128>>>r%8),r+=1}};return e},M=function(t){var r=a,e=t,n={getMode:function(){return r},getLength:function(t){return e.length},write:function(t){for(var r=e,n=0;n+2<r.length;)t.put(o(r.substring(n,n+3)),10),n+=3;n<r.length&&(r.length-n==1?t.put(o(r.substring(n,n+1)),4):r.length-n==2&&t.put(o(r.substring(n,n+2)),7))}},o=function(t){for(var r=0,e=0;e<t.length;e+=1)r=10*r+i(t.charAt(e));return r},i=function(t){if("0"<=t&&t<="9")return t.charCodeAt(0)-"0".charCodeAt(0);throw"illegal char :"+t};return n},x=function(t){var r=u,e=t,n={getMode:function(){return r},getLength:function(t){return e.length},write:function(t){for(var r=e,n=0;n+1<r.length;)t.put(45*o(r.charAt(n))+o(r.charAt(n+1)),11),n+=2;n<r.length&&t.put(o(r.charAt(n)),6)}},o=function(t){if("0"<=t&&t<="9")return t.charCodeAt(0)-"0".charCodeAt(0);if("A"<=t&&t<="Z")return t.charCodeAt(0)-"A".charCodeAt(0)+10;switch(t){case" ":return 36;case"$":return 37;case"%":return 38;case"*":return 39;case"+":return 40;case"-":return 41;case".":return 42;case"/":return 43;case":":return 44;default:throw"illegal char :"+t}};return n},m=function(r){var e=f,n=t.stringToBytes(r),o={getMode:function(){return e},getLength:function(t){return n.length},write:function(t){for(var r=0;r<n.length;r+=1)t.put(n[r],8)}};return o},L=function(r){var e=c,n=t.stringToBytesFuncs.SJIS;if(!n)throw"sjis not supported.";!function(){var t=n("友");if(2!=t.length||38726!=(t[0]<<8|t[1]))throw"sjis not supported."}();var o=n(r),i={getMode:function(){return e},getLength:function(t){return~~(o.length/2)},write:function(t){for(var r=o,e=0;e+1<r.length;){var n=(255&r[e])<<8|255&r[e+1];if(33088<=n&&n<=40956)n-=33088;else{if(!(57408<=n&&n<=60351))throw"illegal char at "+(e+1)+"/"+n;n-=49472}n=192*(n>>>8&255)+(255&n),t.put(n,13),e+=2}if(e<r.length)throw"illegal char at "+(e+1)}};return i};return t.stringToBytes=(t.stringToBytesFuncs={default:function(t){for(var r=[],e=0;e<t.length;e+=1){var n=t.charCodeAt(e);r.push(255&n)}return r}}).default,t}();qrcode.stringToBytesFuncs["UTF-8"]=function(t){return function(t){for(var r=[],e=0;e<t.length;e++){var n=t.charCodeAt(e);n<128?r.push(n):n<2048?r.push(192|n>>6,128|63&n):n<55296||n>=57344?r.push(224|n>>12,128|n>>6&63,128|63&n):(e++,n=65536+((1023&n)<<10|1023&t.charCodeAt(e)),r.push(240|n>>18,128|n>>12&63,128|n>>6&63,128|63&n))}return r}(t)};';

// Default configuration
function getDefaultConfig(): array {
    return [
        'api_token' => bin2hex(random_bytes(32)), // Generated on first run
        'rate_limit_requests' => 60,              // Max requests per window
        'rate_limit_window' => 3600,              // Window in seconds (1 hour)
        'admin_path' => 'admin',                       // Set this to change the path name to access admin panel
        'admin_show_link' => true,                   // Set this to show the Admin Link
        'admin_password' => '',                   // Set this to enable admin panel
        'login_max_attempts' => 3,
        'login_lockout_window' => 3600,
    ];
}



// Load/save configuration
function loadConfig(): array {
    if (!file_exists(CONFIG_FILE)) {
        $config = getDefaultConfig();
        saveConfig($config);
        return $config;
    }
    return json_decode(file_get_contents(CONFIG_FILE), true) ?: getDefaultConfig();
}

function saveConfig(array $config): void {
    file_put_contents(CONFIG_FILE, json_encode($config, JSON_PRETTY_PRINT));
    // chmod(CONFIG_FILE, 0600); // Restrict permissions - depending on server config, might make the config file not accessible
}

// Load/save URLs
function loadUrls(): array {
    if (!file_exists(DATA_FILE)) return [];
    $data = file_get_contents(DATA_FILE);
    return json_decode($data, true) ?: [];
}

function saveUrls(array $urls): void {
    file_put_contents(DATA_FILE, json_encode($urls, JSON_PRETTY_PRINT));
}

// Rate limiting using file-based storage
function checkRateLimit(string $identifier): array {
    $config = loadConfig();
    $limits = file_exists(RATE_LIMITS_FILE) ? json_decode(file_get_contents(RATE_LIMITS_FILE), true) ?: [] : [];
    
    $now = time();
    $windowStart = $now - $config['rate_limit_window'];
    
    // Clean old entries
    foreach ($limits as $id => $data) {
        if ($data['window_start'] < $windowStart) {
            unset($limits[$id]);
        }
    }
    
    if (!isset($limits[$identifier])) {
        $limits[$identifier] = ['count' => 0, 'window_start' => $now];
    }
    
    // Reset if window expired
    if ($limits[$identifier]['window_start'] < $windowStart) {
        $limits[$identifier] = ['count' => 0, 'window_start' => $now];
    }
    
    $limits[$identifier]['count']++;
    file_put_contents(RATE_LIMITS_FILE, json_encode($limits));
    
    $remaining = max(0, $config['rate_limit_requests'] - $limits[$identifier]['count']);
    $exceeded = $limits[$identifier]['count'] > $config['rate_limit_requests'];
    
    return [
        'exceeded' => $exceeded,
        'remaining' => $remaining,
        'reset' => $limits[$identifier]['window_start'] + $config['rate_limit_window']
    ];
}

// Generate short code
function generateCode(int $length = 6): string {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $code;
}

// Validate custom code
function isValidCode(string $code): bool {
    return preg_match('/^[a-zA-Z0-9_-]{1,50}$/', $code);
}

// Get base URL
function getBaseUrl(): string {
    if (BASE_URL) return rtrim(BASE_URL, '/') . '/';
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = dirname($_SERVER['SCRIPT_NAME']);
    return $protocol . '://' . $host . rtrim($script, '/') . '/';
}

// API Response helper
function jsonResponse(array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Get request headers (compatible with all server configs)
function getRequestHeaders(): array {
    $headers = [];
    
    // Try getallheaders() first (Apache)
    if (function_exists('getallheaders')) {
        foreach (getallheaders() as $key => $value) {
            $headers[strtolower($key)] = $value;
        }
    }
    
    // Fallback: parse from $_SERVER (works with nginx/PHP-FPM)
    foreach ($_SERVER as $key => $value) {
        if (strpos($key, 'HTTP_') === 0) {
            $headerKey = strtolower(str_replace('_', '-', substr($key, 5)));
            $headers[$headerKey] = $value;
        }
    }
    
    return $headers;
}

// Verify API token
function verifyApiToken(): bool {
    $config = loadConfig();
    $headers = getRequestHeaders();
    $token = $headers['authorization'] ?? '';
    
    // Support "Bearer TOKEN" format
    if (stripos($token, 'Bearer ') === 0) {
        $token = substr($token, 7);
    }
    
    // Also check X-API-Token header
    if (!$token) {
        $token = $headers['x-api-token'] ?? '';
    }
    
    if (!$token) {
        return false;
    }
    
    return hash_equals($config['api_token'], $token);
}

// Get client identifier for rate limiting
function getClientIdentifier(): string {
    $headers = getRequestHeaders();
    $token = $headers['authorization'] ?? $headers['x-api-token'] ?? '';
    if ($token) return 'token:' . substr(hash('sha256', $token), 0, 16);
    return 'ip:' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
}




// ============================================
// ROUTING
// ============================================

$urls = loadUrls();
$config = loadConfig();

// Parse request
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$scriptDir = dirname($scriptName);
$method = $_SERVER['REQUEST_METHOD'];

// Extract path
if ($scriptDir !== '/' && strpos($requestUri, $scriptDir) === 0) {
    $path = substr($requestUri, strlen($scriptDir));
} else {
    $path = $requestUri;
}

$path = ltrim($path, '/');
$path = strtok($path, '?');
$path = strtok($path, '#');
$baseName = basename($scriptName);
if (strpos($path, $baseName) === 0) {
    $path = substr($path, strlen($baseName));
}
$path = trim($path, '/');

// ============================================
// API ENDPOINTS
// ============================================

if (strpos($path, 'api/') === 0) {
    $apiPath = substr($path, 4);
    
    // Rate limiting for API
    $rateLimit = checkRateLimit(getClientIdentifier());
    header('X-RateLimit-Remaining: ' . $rateLimit['remaining']);
    header('X-RateLimit-Reset: ' . $rateLimit['reset']);
    
    if ($rateLimit['exceeded']) {
        jsonResponse([
            'error' => 'Rate limit exceeded',
            'retry_after' => $rateLimit['reset'] - time()
        ], 429);
    }
    
    // Verify API token for all API requests
    if (!verifyApiToken()) {
        jsonResponse(['error' => 'Invalid or missing API token'], 401);
    }
    
    // POST /api/cutitoff - Create short URL
    if ($apiPath === 'cutitoff' && $method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['url'])) {
            jsonResponse(['error' => 'Missing required field: url'], 400);
        }
        
        $url = filter_var(trim($input['url']), FILTER_VALIDATE_URL);
        if (!$url) {
            jsonResponse(['error' => 'Invalid URL format'], 400);
        }
        
        $customCode = isset($input['code']) ? trim($input['code']) : null;
        $title = isset($input['title']) ? trim($input['title']) : '';
        $description = isset($input['description']) ? trim($input['description']) : '';
        $allowOverwrite = isset($input['allow_overwrite']) ? (bool)$input['allow_overwrite'] : false;
        
        // Handle custom code
        if ($customCode) {
            if (!isValidCode($customCode)) {
                jsonResponse(['error' => 'Invalid code format. Use alphanumeric, dash, underscore only (1-50 chars)'], 400);
            }
            
            // Check if code exists and if it can be overwritten
            if (isset($urls[$customCode])) {
                $existingData = is_array($urls[$customCode]) ? $urls[$customCode] : ['url' => $urls[$customCode]];
                $canOverwrite = $existingData['allow_overwrite'] ?? false;
                
                if (!$canOverwrite) {
                    jsonResponse(['error' => 'Code already exists and is protected from overwriting.'], 409);
                }
            }
            
            $code = $customCode;
        } else {
            // Check if URL already shortened
            foreach ($urls as $existingCode => $data) {
                $existingUrl = is_array($data) ? $data['url'] : $data;
                if ($existingUrl === $url) {
                    $code = $existingCode;
                    break;
                }
            }
            
            if (!isset($code)) {
                do {
                    $code = generateCode();
                } while (isset($urls[$code]));
            }
        }
        
        // Store with metadata
        $urls[$code] = [
            'url' => $url,
            'title' => $title,
            'description' => $description,
            'allow_overwrite' => $allowOverwrite,
            'created_at' => isset($urls[$code]) && is_array($urls[$code]) 
                ? $urls[$code]['created_at'] 
                : date('c'),
            'updated_at' => date('c'),
            'clicks' => isset($urls[$code]) && is_array($urls[$code]) 
                ? ($urls[$code]['clicks'] ?? 0) 
                : 0
        ];
        
        saveUrls($urls);
        
        jsonResponse([
            'success' => true,
            'code' => $code,
            'short_url' => getBaseUrl() . $code,
            'original_url' => $url,
            'title' => $title,
            'description' => $description,
            'allow_overwrite' => $allowOverwrite
        ], 201);
    }
    
    // GET /api/urls - List all URLs
    if ($apiPath === 'urls' && $method === 'GET') {
        $result = [];
        foreach ($urls as $code => $data) {
            if (is_array($data)) {
                $result[$code] = array_merge($data, ['short_url' => getBaseUrl() . $code]);
            } else {
                $result[$code] = [
                    'url' => $data,
                    'short_url' => getBaseUrl() . $code,
                    'title' => '',
                    'description' => ''
                ];
            }
        }
        jsonResponse(['urls' => $result]);
    }
    
    // GET /api/urls/{code} - Get single URL info
    if (preg_match('/^urls\/([^\/]+)$/', $apiPath, $matches) && $method === 'GET') {
        $code = $matches[1];
        if (!isset($urls[$code])) {
            jsonResponse(['error' => 'URL not found'], 404);
        }
        
        $data = is_array($urls[$code]) ? $urls[$code] : ['url' => $urls[$code]];
        $data['code'] = $code;
        $data['short_url'] = getBaseUrl() . $code;
        
        jsonResponse($data);
    }
    
    // DELETE /api/urls/{code} - Delete URL
    if (preg_match('/^urls\/([^\/]+)$/', $apiPath, $matches) && $method === 'DELETE') {
        $code = $matches[1];
        if (!isset($urls[$code])) {
            jsonResponse(['error' => 'URL not found'], 404);
        }
        
        unset($urls[$code]);
        saveUrls($urls);
        
        jsonResponse(['success' => true, 'message' => 'URL deleted']);
    }
    
    // PUT /api/urls/{code} - Update URL metadata
    if (preg_match('/^urls\/([^\/]+)$/', $apiPath, $matches) && $method === 'PUT') {
        $code = $matches[1];
        if (!isset($urls[$code])) {
            jsonResponse(['error' => 'URL not found'], 404);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $data = is_array($urls[$code]) ? $urls[$code] : ['url' => $urls[$code]];
        
        if (isset($input['url'])) {
            $url = filter_var(trim($input['url']), FILTER_VALIDATE_URL);
            if (!$url) jsonResponse(['error' => 'Invalid URL format'], 400);
            $data['url'] = $url;
        }
        if (isset($input['title'])) $data['title'] = trim($input['title']);
        if (isset($input['description'])) $data['description'] = trim($input['description']);
        $data['updated_at'] = date('c');
        
        $urls[$code] = $data;
        saveUrls($urls);
        
        jsonResponse(['success' => true, 'data' => array_merge($data, ['code' => $code, 'short_url' => getBaseUrl() . $code])]);
    }
    
    jsonResponse(['error' => 'Endpoint not found'], 404);
}

// ============================================
// ADMIN PANEL
// ============================================

if ($path === $config['admin_path']) {
    session_start();
    
    // Check if admin password is set
    if (empty($config['admin_password'])) {
        echo '<!DOCTYPE html><html><head><title>Admin Setup</title>
		  <link rel="manifest" href="manifest.json">
		<link rel="apple-touch-icon" href="apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="512x512" href="favicon.png"/>
		<link rel="shortcut icon" href="favicon.ico"/>
        <style>body{font-family:system-ui;padding:40px;max-width:600px;margin:0 auto;background:#1a1a2e;color:#eee;}
        .box{background:#16213e;padding:30px;border-radius:12px;}
        code{background:#0f3460;padding:2px 8px;border-radius:4px;}</style></head>
        <body><div class="box"><h2>⚠️ Admin Not Configured</h2>
        <p>To enable the admin panel, edit <code>config.json</code> and set an <code>admin_password</code>.</p>
        <p>Your API token is also in that file.</p></div></body></html>';
        exit;
    }


	// Handle login
	if ($method === 'POST' && isset($_POST['admin_login'])) {
		$loginIdentifier = 'login:' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
		$maxAttempts = $config['login_max_attempts'] ?? 3;
		$lockoutWindow = $config['login_lockout_window'] ?? 3600;
		
		// Check login rate limit
		$limits = file_exists(RATE_LIMITS_FILE) ? json_decode(file_get_contents(RATE_LIMITS_FILE), true) ?: [] : [];
		$now = time();
		
		// Clean expired entry
		if (isset($limits[$loginIdentifier]) && $limits[$loginIdentifier]['window_start'] < ($now - $lockoutWindow)) {
			unset($limits[$loginIdentifier]);
		}
		
		$attempts = $limits[$loginIdentifier]['count'] ?? 0;
		
		if ($attempts >= $maxAttempts) {
			$remainingTime = ($limits[$loginIdentifier]['window_start'] + $lockoutWindow) - $now;
			$_SESSION['admin_message'] = 'login_locked';
			$_SESSION['lockout_remaining'] = $remainingTime;
			header('Location: ' . $_SERVER['REQUEST_URI']);
			exit;
		}
		
		if (hash_equals($config['admin_password'], $_POST['password'])) {
			// Success - clear login attempts
			unset($limits[$loginIdentifier]);
			file_put_contents(RATE_LIMITS_FILE, json_encode($limits));
			$_SESSION['admin_logged_in'] = true;
		} else {
			// Failed - increment attempts
			if (!isset($limits[$loginIdentifier])) {
				$limits[$loginIdentifier] = ['count' => 0, 'window_start' => $now];
			}
			$limits[$loginIdentifier]['count']++;
			file_put_contents(RATE_LIMITS_FILE, json_encode($limits));
			
			$remainingAttempts = $maxAttempts - $limits[$loginIdentifier]['count'];
			$_SESSION['admin_message'] = 'login_failed';
			$_SESSION['login_attempts_remaining'] = $remainingAttempts;
		}
		
		header('Location: ' . $_SERVER['REQUEST_URI']);
		exit;
	}

    
    // Handle logout
    if (isset($_GET['logout'])) {
        unset($_SESSION['admin_logged_in']);
        header('Location: ?');
        exit;
    }
		
	// Check authentication
	$isLoggedIn = $_SESSION['admin_logged_in'] ?? false;

	// Get flash message from session
	$adminMessage = $_SESSION['admin_message'] ?? '';
	unset($_SESSION['admin_message']);

	// Handle admin actions with PRG pattern
	if ($isLoggedIn && $method === 'POST') {
		// Delete URL
		if (isset($_POST['delete_code'])) {
			$code = $_POST['delete_code'];
			if (isset($urls[$code])) {
				unset($urls[$code]);
				saveUrls($urls);
				$_SESSION['admin_message'] = 'deleted';
			}
			header('Location: ' . $_SERVER['REQUEST_URI']);
			exit;
		}
		
		// Update URL
		if (isset($_POST['update_code'])) {
			$oldCode = $_POST['update_code'];
			$newCode = isset($_POST['new_code']) ? trim($_POST['new_code']) : $oldCode;
			$newUrl = isset($_POST['new_url']) ? trim($_POST['new_url']) : '';
			
			if (isset($urls[$oldCode])) {
				$data = is_array($urls[$oldCode]) ? $urls[$oldCode] : ['url' => $urls[$oldCode]];
				
				// Validate and update destination URL
				if ($newUrl) {
					$validatedUrl = filter_var($newUrl, FILTER_VALIDATE_URL);
					if (!$validatedUrl) {
						$_SESSION['admin_message'] = 'invalid_url';
						header('Location: ' . $_SERVER['REQUEST_URI']);
						exit;
					}
					$data['url'] = $validatedUrl;
				}
				
				// Validate and update short code
				if ($newCode !== $oldCode) {
					if (!isValidCode($newCode)) {
						$_SESSION['admin_message'] = 'invalid_new_code';
						header('Location: ' . $_SERVER['REQUEST_URI']);
						exit;
					}
					if (isset($urls[$newCode])) {
						$_SESSION['admin_message'] = 'code_exists';
						header('Location: ' . $_SERVER['REQUEST_URI']);
						exit;
					}
					// Remove old code and add new one
					unset($urls[$oldCode]);
				}
				
				$data['title'] = trim($_POST['title'] ?? '');
				$data['description'] = trim($_POST['description'] ?? '');
				$data['allow_overwrite'] = isset($_POST['allow_overwrite']);
				$data['updated_at'] = date('c');
				$urls[$newCode] = $data;
				saveUrls($urls);
				$_SESSION['admin_message'] = 'updated';
			}
			header('Location: ' . $_SERVER['REQUEST_URI']);
			exit;
		}
	}


	// Update settings (API token, rate limits)
	if (isset($_POST['update_settings'])) {
		if (isset($_POST['regenerate_token']) && $_POST['regenerate_token'] === '1') {
			$config['api_token'] = bin2hex(random_bytes(32));
		}
		
		if (isset($_POST['rate_limit_requests'])) {
			$config['rate_limit_requests'] = max(1, intval($_POST['rate_limit_requests']));
		}
		
		if (isset($_POST['rate_limit_window'])) {
			$config['rate_limit_window'] = max(60, intval($_POST['rate_limit_window']));
		}
		
		saveConfig($config);
		$_SESSION['admin_message'] = 'settings_updated';
		header('Location: ' . $_SERVER['REQUEST_URI']);
		exit;
	}


	// Reload URLs after potential changes
	$urls = loadUrls();
    
    ?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Cut It Off - URL Shortener</title>
	<link rel="manifest" href="manifest.json">
	<link rel="apple-touch-icon" href="apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="512x512" href="favicon.png"/>
	<link rel="shortcut icon" href="favicon.ico"/>	
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">	
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
	<!-- Cut It Off version <?php echo $version.' '.$versiondate;?>-->
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        :root {
            --bg-primary: #0a0a0f;
            --bg-secondary: #12121a;
            --bg-tertiary: #1a1a25;
            --accent: #6366f1;
            --accent-hover: #818cf8;
            --danger: #ef4444;
            --danger-hover: #f87171;
            --success: #10b981;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --border: #2a2a3a;
        }
		
		/* Light theme */
		[data-theme="light"] {
			--bg-primary: #f5f5f7;
			--bg-secondary: #ffffff;
			--bg-tertiary: #e8e8ec;
			--accent: #6366f1;
			--accent-hover: #4f46e5;
			--danger: #dc2626;
			--danger-hover: #b91c1c;
			--success: #16a34a;
			--text-primary: #1a1a1a;
			--text-secondary: #4a4a4a;
			--text-muted: #6b6b6b;
			--border: rgba(0,0,0,0.1);
		}
		
		/* Theme toggle button */
		.icon-btn {
			width: 40px;
			height: 40px;
			border-radius: 8px;
			border: 1px solid var(--border);
			background: var(--bg-tertiary);
			color: var(--text-secondary);
			cursor: pointer;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 16px;
			transition: all 0.2s ease;
		}
		
		.icon-btn:hover {
			background: var(--accent);
			color: white;
			border-color: var(--accent);
		}
        
        body {
            font-family: 'Space Grotesk', system-ui, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }
        
        h1 {
            font-size: 1.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        h1 span {
            background: linear-gradient(135deg, var(--accent), #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .login-box {
            background: var(--bg-secondary);
            padding: 50px;
            border-radius: 16px;
            max-width: 400px;
            margin: 100px auto;
            border: 1px solid var(--border);
        }
        
        .login-box h2 {
            margin-bottom: 30px;
            font-size: 1.5rem;
        }
        
        input, textarea {
            width: 100%;
            padding: 14px 16px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-primary);
            font-family: inherit;
            font-size: 15px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }
        
        button, .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-primary {
            background: var(--accent);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
        }
        
        .btn-danger {
            background: transparent;
            color: var(--danger);
            border: 1px solid var(--danger);
            padding: 8px 16px;
            font-size: 13px;
        }
        
        .btn-danger:hover {
            background: var(--danger);
            color: white;
        }
        
        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }
        
        .btn-secondary:hover {
            background: var(--border);
        }
        
        .url-grid {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        
        .url-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            transition: border-color 0.2s;
        }
        
        .url-card:hover {
            border-color: var(--accent);
        }
        
        .url-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }
        
        .url-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--accent);
        }
        
        .url-meta {
            display: flex;
            gap: 16px;
            font-size: 13px;
            color: var(--text-muted);
        }
        
        .url-title {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 4px;
            color: var(--text-primary);
        }
        
        .url-description {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 12px;
        }
        
        .url-target {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            color: var(--text-muted);
            word-break: break-all;
            background: var(--bg-tertiary);
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 16px;
        }
        
        .url-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .edit-form {
            display: none;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }
        
        .edit-form.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
        }
        
        textarea {
            min-height: 80px;
            resize: vertical;
        }
        
        .alert {
            padding: 14px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid var(--success);
            color: var(--success);
        }
        
        .api-info {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 30px;
        }
        
        .api-info h3 {
            font-size: 1rem;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .api-token {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            background: var(--bg-tertiary);
            padding: 12px 16px;
            border-radius: 6px;
            word-break: break-all;
            position: relative;
        }
        
        .copy-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }
        
        .empty-state h3 {
            font-size: 1.2rem;
            margin-bottom: 8px;
            color: var(--text-secondary);
        }
        
        .stats {
            display: flex;
            gap: 24px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px 28px;
            min-width: 140px;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 600;
            color: var(--accent);
        }
        
        .stat-label {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }
        
        .back-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .back-link:hover {
            color: var(--accent);
        }
        
        @media (max-width: 640px) {
            .url-header {
                flex-direction: column;
                gap: 12px;
            }
            
            .stats {
                flex-direction: column;
                gap: 12px;
            }
        }
			.footer {margin-top: 24px;text-align: center;}
        .footer a {color: var(--text-muted);text-decoration: none;font-size: 13px;transition: color 0.2s;}
        .footer a:hover {color: var(--accent-light);}
		
		.qr-container {
			display: none;
			flex-direction: column;
			align-items: center;
			gap: 12px;
			margin-top: 16px;
			padding-top: 16px;
			border-top: 1px solid var(--border);
		}

		.qr-container.active {
			display: flex;
		}

		.qr-canvas {
			background: white;
			padding: 12px;
			border-radius: 8px;
			transition: transform 0.3s ease;
			cursor: pointer;
		}

		.qr-canvas:hover {
			transform: scale(1.5);
			box-shadow: 0 8px 32px rgba(0,0,0,0.3);
			z-index: 10;
			position: relative;
		}

		.qr-canvas svg {
			display: block;
		}

		.qr-download-btn {
			display: flex;
			align-items: center;
			gap: 6px;
			font-size: 12px;
			padding: 8px 16px;
		}

		.api-docs {
			margin-top: 20px;
			border-top: 1px solid var(--border);
			padding-top: 16px;
		}

		.api-docs-toggle {
			display: flex;
			align-items: center;
			justify-content: space-between;
			width: 100%;
			padding: 12px 16px;
			background: var(--bg-tertiary);
			border: 1px solid var(--border);
			border-radius: 8px;
			color: var(--text-secondary);
			font-size: 14px;
			cursor: pointer;
			transition: all 0.2s;
		}

		.api-docs-toggle:hover {
			border-color: var(--accent);
			color: var(--text-primary);
		}

		.api-docs-toggle i {
			transition: transform 0.3s;
		}

		.api-docs-toggle.active i {
			transform: rotate(180deg);
		}

		.api-docs-content {
			display: none;
			margin-top: 16px;
			padding: 16px;
			background: var(--bg-tertiary);
			border-radius: 8px;
		}

		.api-docs-content.show {
			display: block;
			animation: fadeIn 0.3s ease;
		}

		.api-docs-intro {
			font-size: 13px;
			color: var(--text-muted);
			margin-bottom: 16px;
			padding-bottom: 12px;
			border-bottom: 1px solid var(--border);
		}

		.api-endpoint {
			display: flex;
			align-items: center;
			gap: 12px;
			padding: 10px 0;
		}

		.api-method {
			font-size: 11px;
			font-weight: 600;
			padding: 4px 8px;
			border-radius: 4px;
			min-width: 60px;
			text-align: center;
		}

		.api-method.get { background: rgba(16, 185, 129, 0.2); color: #10b981; }
		.api-method.post { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
		.api-method.put { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
		.api-method.delete { background: rgba(239, 68, 68, 0.2); color: #ef4444; }

		.api-endpoint code {
			font-family: 'JetBrains Mono', monospace;
			font-size: 13px;
			color: var(--text-primary);
		}

		.api-desc {
			font-size: 12px;
			color: var(--text-muted);
			margin-left: auto;
		}

		.api-body {
			font-size: 12px;
			color: var(--text-muted);
			padding: 4px 0 10px 76px;
			border-bottom: 1px solid var(--border);
		}

		.api-body code {
			font-size: 11px;
			background: var(--bg-secondary);
			padding: 2px 6px;
			border-radius: 4px;
		}

		.api-note {
			font-size: 12px;
			color: var(--text-muted);
			margin-top: 16px;
			padding-top: 12px;
			border-top: 1px solid var(--border);
		}
		

		.settings-group {
			margin-bottom: 20px;
			padding-bottom: 20px;
			border-bottom: 1px solid var(--border);
		}

		.settings-group:last-of-type {
			border-bottom: none;
		}

		.settings-group > label {
			display: block;
			font-size: 13px;
			font-weight: 500;
			color: var(--text-secondary);
			margin-bottom: 10px;
		}

		.settings-row {
			display: flex;
			align-items: center;
			gap: 10px;
			flex-wrap: wrap;
		}

		.input-small {
			width: 100px;
			padding: 10px 12px;
			background: var(--bg-tertiary);
			border: 1px solid var(--border);
			border-radius: 6px;
			color: var(--text-primary);
			font-family: 'JetBrains Mono', monospace;
			font-size: 14px;
		}

		.input-small:focus {
			outline: none;
			border-color: var(--accent);
		}

		.settings-hint {
			font-size: 13px;
			color: var(--text-muted);
		}

		.btn-sm {
			margin-top: 10px;
			padding: 8px 14px;
			font-size: 12px;
		}

		.api-token {
			margin-bottom: 10px;
		}		
		
		
		.alert-error {
			background: rgba(239, 68, 68, 0.1);
			border: 1px solid var(--danger);
			color: var(--danger);
		}
					
		
    </style>
</head>
<body>
    <div class="container">
		<?php if (!$isLoggedIn): ?>
		<div class="login-box">
			<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
				<h2 style="margin:0;">🔐 Admin Login</h2>
				<button class="icon-btn" id="theme-toggle" title="Toggle theme">
					<i class="fas fa-sun"></i>
				</button>
			</div>
			
			<?php if ($adminMessage === 'login_failed'): ?>
			<div class="alert alert-error" style="margin-bottom:16px;">✕ Invalid password<?php if (isset($_SESSION['login_attempts_remaining'])): ?> (<?= $_SESSION['login_attempts_remaining'] ?> attempt<?= $_SESSION['login_attempts_remaining'] !== 1 ? 's' : '' ?> remaining)<?php unset($_SESSION['login_attempts_remaining']); endif; ?></div>
			<?php elseif ($adminMessage === 'login_locked'): ?>
			<div class="alert alert-error" style="margin-bottom:16px;">✕ Too many failed attempts. Please try again in <?= ceil(($_SESSION['lockout_remaining'] ?? 3600) / 60) ?> minute<?= ceil(($_SESSION['lockout_remaining'] ?? 3600) / 60) !== 1 ? 's' : '' ?>.</div>
			<?php unset($_SESSION['lockout_remaining']); endif; ?>
			
			<form method="post" id="login-form">
				<div class="form-group">
					<label for="password">Password</label>
					<input type="password" name="password" id="password" required autofocus>
				</div>
				<button type="submit" name="admin_login" class="btn btn-primary" style="width:100%">
					Login
				</button>
			</form>
		</div>

		<div class="footer">
			<a href="./">Back to homepage →</a>
		</div>		
		<?php else: ?>
        
        <header>
            <h1>✂️ <span>Cut It Off Admin</span></h1>
            <div style="display:flex;gap:16px;align-items:center;">
                <button class="icon-btn" id="theme-toggle" title="Toggle theme">
                    <i class="fas fa-sun"></i>
                </button>
                <a href="<?= htmlspecialchars(dirname($_SERVER['SCRIPT_NAME'])) ?>" class="back-link">← Back to Cut It Off homepage</a>
                <a href="?logout=1" class="btn btn-secondary">Logout</a>
            </div>
        </header>
        
		<?php if ($adminMessage === 'deleted'): ?>
		<div class="alert alert-success">✓ URL deleted successfully</div>
		<?php elseif ($adminMessage === 'updated'): ?>
		<div class="alert alert-success">✓ URL updated successfully</div>
		<?php elseif ($adminMessage === 'settings_updated'): ?>
		<div class="alert alert-success">✓ Settings saved successfully</div>
		<?php elseif ($adminMessage === 'invalid_url'): ?>
		<div class="alert alert-error">✕ Invalid destination URL format</div>
		<?php elseif ($adminMessage === 'invalid_new_code'): ?>
		<div class="alert alert-error">✕ Invalid short code format. Use only letters, numbers, dashes and underscores (1-50 characters)</div>
		<?php elseif ($adminMessage === 'code_exists'): ?>
		<div class="alert alert-error">✕ Short code already exists. Please choose a different one.</div>
		<?php endif; ?>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-value"><?= count($urls) ?></div>
                <div class="stat-label">Total URLs</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= array_sum(array_map(function($d) { 
                    return is_array($d) ? ($d['clicks'] ?? 0) : 0; 
                }, $urls)) ?></div>
                <div class="stat-label">Total Clicks</div>
            </div>
        </div>
						
		<div class="api-info">
			<h3>⚙️ API Settings</h3>
			
			<form method="post" id="settings-form">
				<input type="hidden" name="update_settings" value="1">
				<input type="hidden" name="regenerate_token" id="regenerate_token" value="0">
				
				<div class="settings-group">
					<label>API Token</label>
					<div class="api-token">
						<?= htmlspecialchars($config['api_token']) ?>
						<button type="button" class="btn btn-secondary copy-btn" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($config['api_token']) ?>'); this.textContent='Copied!';">
							Copy
						</button>
					</div>
					<button type="button" class="btn btn-danger btn-sm" onclick="confirmRegenerateToken()">
						🔄 Regenerate Token
					</button>
				</div>
				
				<div class="settings-group">
					<label for="rate_limit_requests">Rate Limit (requests per window)</label>
					<div class="settings-row">
						<input type="number" name="rate_limit_requests" id="rate_limit_requests" 
							   value="<?= $config['rate_limit_requests'] ?>" min="1" class="input-small">
						<span class="settings-hint">requests per</span>
						<input type="number" name="rate_limit_window" id="rate_limit_window" 
							   value="<?= $config['rate_limit_window'] ?>" min="60" step="60" class="input-small">
						<span class="settings-hint">seconds</span>
					</div>
				</div>
				
				<button type="submit" class="btn btn-primary">💾 Save Settings</button>
			</form>
			
			<div class="api-docs">
				<button type="button" class="api-docs-toggle" onclick="toggleApiDocs()">
					<span>📚 API Documentation</span>
					<i class="fas fa-chevron-down" id="api-docs-icon"></i>
				</button>
				
				<div class="api-docs-content" id="api-docs-content">
					<p class="api-docs-intro">Use header <code>Authorization: Bearer YOUR_TOKEN</code> or <code>X-API-Token: YOUR_TOKEN</code></p>
					
					<div class="api-endpoint">
						<div class="api-method post">POST</div>
						<code>/api/cutitoff</code>
						<span class="api-desc">Create short URL</span>
					</div>
					<div class="api-body">
						Body: <code>{"url": "https://...", "code": "custom", "title": "...", "description": "...", "allow_overwrite": false}</code>
					</div>
					
					<div class="api-endpoint">
						<div class="api-method get">GET</div>
						<code>/api/urls</code>
						<span class="api-desc">List all URLs</span>
					</div>
					
					<div class="api-endpoint">
						<div class="api-method get">GET</div>
						<code>/api/urls/{code}</code>
						<span class="api-desc">Get URL info</span>
					</div>
					
					<div class="api-endpoint">
						<div class="api-method put">PUT</div>
						<code>/api/urls/{code}</code>
						<span class="api-desc">Update URL</span>
					</div>
					<div class="api-body">
						Body: <code>{"url": "...", "title": "...", "description": "..."}</code>
					</div>
					
					<div class="api-endpoint">
						<div class="api-method delete">DELETE</div>
						<code>/api/urls/{code}</code>
						<span class="api-desc">Delete URL</span>
					</div>
					
					<p class="api-note">{code} corresponds to the URL short code</p>
					<p class="api-note">Current: <?= $config['rate_limit_requests'] ?> requests per <?= $config['rate_limit_window'] >= 3600 ? ($config['rate_limit_window'] / 3600) . ' hour(s)' : ($config['rate_limit_window'] / 60) . ' min' ?></p>
				</div>
			</div>
		</div>
        
        <div class="url-grid">
            <?php if (empty($urls)): ?>
            <div class="empty-state">
                <h3>No URLs yet</h3>
                <p>Create your first short URL to see it here.</p>
            </div>
            <?php else: ?>
            <?php foreach ($urls as $code => $data): 
                $url = is_array($data) ? $data['url'] : $data;
                $title = is_array($data) ? ($data['title'] ?? '') : '';
                $description = is_array($data) ? ($data['description'] ?? '') : '';
                $clicks = is_array($data) ? ($data['clicks'] ?? 0) : 0;
                $allowOverwrite = is_array($data) ? ($data['allow_overwrite'] ?? false) : false;
                $createdAt = is_array($data) && isset($data['created_at']) 
                    ? date('M j, Y', strtotime($data['created_at'])) 
                    : 'Unknown';
            ?>
            <div class="url-card">
                <div class="url-header">
                    <div>
                        <div class="url-code">/<?= htmlspecialchars($code) ?><?= $allowOverwrite ? ' <span style="font-size:11px;color:var(--text-muted);font-weight:400;">(editable)</span>' : ' <span style="font-size:11px;color:var(--success);font-weight:400;">🔒</span>' ?></div>
                        <?php if ($title): ?>
                        <div class="url-title"><?= htmlspecialchars($title) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="url-meta">
                        <span>📊 <?= $clicks ?> clicks</span>
                        <span>📅 <?= $createdAt ?></span>
                    </div>
                </div>
                
                <?php if ($description): ?>
                <div class="url-description"><?= htmlspecialchars($description) ?></div>
                <?php endif; ?>
                
                <div class="url-target"><?= htmlspecialchars($url) ?></div>
								
				<div class="url-actions">
					<a href="<?= htmlspecialchars(getBaseUrl() . $code) ?>" target="_blank" class="btn btn-secondary">
						Open Link ↗
					</a>
					<button class="btn btn-secondary" onclick="copyLink('<?= htmlspecialchars(getBaseUrl() . $code) ?>', this)">
						📋 Copy Link
					</button>
					<button class="btn btn-secondary" onclick="toggleQR('<?= htmlspecialchars($code) ?>', '<?= htmlspecialchars(getBaseUrl() . $code) ?>')">
						📱 QR Code
					</button>
					<button class="btn btn-secondary" onclick="toggleEdit('<?= htmlspecialchars($code) ?>')">
						✏️ Edit
					</button>
					<form method="post" style="display:inline" onsubmit="return confirm('Delete this URL?');">
						<input type="hidden" name="delete_code" value="<?= htmlspecialchars($code) ?>">
						<button type="submit" class="btn btn-danger">🗑️ Delete</button>
					</form>
				</div>

				<div class="qr-container" id="qr-<?= htmlspecialchars($code) ?>">
					<div class="qr-canvas" id="qr-canvas-<?= htmlspecialchars($code) ?>"></div>
					<button class="btn btn-secondary qr-download-btn" onclick="downloadQR('<?= htmlspecialchars($code) ?>')">
						<i class="fas fa-download"></i> Download PNG
					</button>
				</div>
                
				<div class="edit-form" id="edit-<?= htmlspecialchars($code) ?>">
					<form method="post">
						<input type="hidden" name="update_code" value="<?= htmlspecialchars($code) ?>">
						<div class="form-group">
							<label for="new-code-<?= htmlspecialchars($code) ?>">Short Code</label>
							<input type="text" name="new_code" id="new-code-<?= htmlspecialchars($code) ?>" 
								   value="<?= htmlspecialchars($code) ?>" pattern="[a-zA-Z0-9_-]{1,50}" required>
							<small style="color:var(--text-muted);font-size:11px;">Letters, numbers, dashes and underscores only</small>
						</div>
						<div class="form-group">
							<label for="new-url-<?= htmlspecialchars($code) ?>">Destination URL</label>
							<input type="url" name="new_url" id="new-url-<?= htmlspecialchars($code) ?>" 
								   value="<?= htmlspecialchars($url) ?>" placeholder="https://example.com/..." required>
						</div>
						<div class="form-group">
							<label for="title-<?= htmlspecialchars($code) ?>">Title</label>
							<input type="text" name="title" id="title-<?= htmlspecialchars($code) ?>" 
								   value="<?= htmlspecialchars($title) ?>" placeholder="Optional title...">
						</div>
						<div class="form-group">
							<label for="desc-<?= htmlspecialchars($code) ?>">Description</label>
							<textarea name="description" id="desc-<?= htmlspecialchars($code) ?>" 
									  placeholder="Optional description..."><?= htmlspecialchars($description) ?></textarea>
						</div>
						<div class="form-group" style="display:flex;align-items:center;gap:10px;">
							<input type="checkbox" name="allow_overwrite" id="overwrite-<?= htmlspecialchars($code) ?>" 
								   <?= $allowOverwrite ? 'checked' : '' ?> style="width:18px;height:18px;">
							<label for="overwrite-<?= htmlspecialchars($code) ?>" style="margin:0;cursor:pointer;">
								Allow this code to be overwritten
							</label>
						</div>
						<div style="display:flex;gap:12px">
							<button type="submit" class="btn btn-primary">Save Changes</button>
							<button type="button" class="btn btn-secondary" onclick="toggleEdit('<?= htmlspecialchars($code) ?>')">Cancel</button>
						</div>
					</form>
				</div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
				
		<script>
			// QR Code Library
			<?php echo $QRcode_library; ?>

			function toggleApiDocs() {
				const content = document.getElementById('api-docs-content');
				const toggle = document.querySelector('.api-docs-toggle');
				content.classList.toggle('show');
				toggle.classList.toggle('active');
			}


			function confirmRegenerateToken() {
				if (confirm('⚠️ WARNING: Regenerating the API token will invalidate all existing integrations using the current token.\n\nThis action cannot be undone. Are you sure you want to continue?')) {
					document.getElementById('regenerate_token').value = '1';
					document.getElementById('settings-form').submit();
				}
			}


			function toggleEdit(code) {
				const form = document.getElementById('edit-' + code);
				form.classList.toggle('active');
			}

			function toggleQR(code, url) {
				const container = document.getElementById('qr-' + code);
				const canvas = document.getElementById('qr-canvas-' + code);
				
				// Toggle visibility
				container.classList.toggle('active');
				
				// Generate QR if becoming visible and not already generated
				if (container.classList.contains('active') && !canvas.querySelector('svg')) {
					try {
						const qr = qrcode(0, 'L');
						qr.addData(url, 'Byte');
						qr.make();
						canvas.innerHTML = qr.createSvgTag({ cellSize: 4, margin: 4 });
					} catch (e) {
						canvas.innerHTML = '<p style="color:var(--text-muted);padding:20px;">Error generating QR</p>';
					}
				}
			}

			function downloadQR(code) {
				const canvas = document.getElementById('qr-canvas-' + code);
				const svg = canvas.querySelector('svg');
				if (!svg) return;
				
				const svgData = new XMLSerializer().serializeToString(svg);
				const imgCanvas = document.createElement('canvas');
				const ctx = imgCanvas.getContext('2d');
				const img = new Image();
				
				img.onload = () => {
					imgCanvas.width = img.width * 2;
					imgCanvas.height = img.height * 2;
					ctx.fillStyle = '#ffffff';
					ctx.fillRect(0, 0, imgCanvas.width, imgCanvas.height);
					ctx.drawImage(img, 0, 0, imgCanvas.width, imgCanvas.height);
					
					const link = document.createElement('a');
					link.download = code + '-qr.png';
					link.href = imgCanvas.toDataURL('image/png');
					link.click();
				};
				
				img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
			}
			
			function copyLink(url, btn) {
				navigator.clipboard.writeText(url).then(() => {
					const originalText = btn.innerHTML;
					btn.innerHTML = '✓ Copied!';
					setTimeout(() => btn.innerHTML = originalText, 2000);
				});
			}			
		</script>
        
        <?php endif; ?>
    </div>
	
	<script>
		// Theme toggle (works for both login and admin pages)
		const themeToggleGlobal = document.getElementById('theme-toggle');
		if (themeToggleGlobal) {
			const htmlEl = document.documentElement;

			// Check for saved theme preference or default to dark
			const savedThemeGlobal = localStorage.getItem('theme') || 'dark';
			htmlEl.setAttribute('data-theme', savedThemeGlobal);
			updateThemeIconGlobal(savedThemeGlobal);

			themeToggleGlobal.addEventListener('click', () => {
				const currentTheme = htmlEl.getAttribute('data-theme');
				const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
				
				htmlEl.setAttribute('data-theme', newTheme);
				localStorage.setItem('theme', newTheme);
				updateThemeIconGlobal(newTheme);
			});

			function updateThemeIconGlobal(theme) {
				const icon = themeToggleGlobal.querySelector('i');
				if (icon) icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
			}
		}
	</script>
</body>
</html>
    <?php
    exit;
}

// ============================================
// REDIRECT HANDLER
// ============================================

if ($path && isset($urls[$path])) {
    $data = is_array($urls[$path]) ? $urls[$path] : ['url' => $urls[$path]];
    
    // Increment click counter
    if (!isset($data['clicks'])) $data['clicks'] = 0;
    $data['clicks']++;
    $urls[$path] = $data;
    saveUrls($urls);
    
    header('Location: ' . $data['url'], true, 301);
    exit;
}

// ============================================
// MAIN SHORTENER UI
// ============================================

session_start();

$message = $_SESSION['form_message'] ?? '';
$shortUrl = $_SESSION['form_shortUrl'] ?? '';
$createdCode = $_SESSION['form_createdCode'] ?? '';

// Clear session data after reading
unset($_SESSION['form_message'], $_SESSION['form_shortUrl'], $_SESSION['form_createdCode']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
    // Rate limiting for form submission
    $rateLimit = checkRateLimit('form:' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    
    if ($rateLimit['exceeded']) {
        $_SESSION['form_message'] = 'rate_limited';
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
    
    $url = filter_var(trim($_POST['url']), FILTER_VALIDATE_URL);
    $customCode = isset($_POST['custom_code']) ? trim($_POST['custom_code']) : '';
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $allowOverwrite = isset($_POST['allow_overwrite']);
    
    if ($url) {
        $code = null;
        $msg = '';
        
        if ($customCode) {
            if (!isValidCode($customCode)) {
                $msg = 'invalid_code';
            } elseif (isset($urls[$customCode])) {
                $existingData = is_array($urls[$customCode]) ? $urls[$customCode] : ['url' => $urls[$customCode]];
                $canOverwrite = $existingData['allow_overwrite'] ?? false;
                
                if (!$canOverwrite) {
                    $msg = 'code_protected';
                } else {
                    $code = $customCode;
                }
            } else {
                $code = $customCode;
            }
        } else {
            foreach ($urls as $existingCode => $data) {
                $existingUrl = is_array($data) ? $data['url'] : $data;
                if ($existingUrl === $url) {
                    $code = $existingCode;
                    break;
                }
            }
            
            if (!$code) {
                do {
                    $code = generateCode();
                } while (isset($urls[$code]));
            }
        }
        
        if ($code && !$msg) {
            $urls[$code] = [
                'url' => $url,
                'title' => $title,
                'description' => $description,
                'allow_overwrite' => $allowOverwrite,
                'created_at' => isset($urls[$code]) && is_array($urls[$code]) 
                    ? $urls[$code]['created_at'] 
                    : date('c'),
                'updated_at' => date('c'),
                'clicks' => isset($urls[$code]) && is_array($urls[$code]) 
                    ? ($urls[$code]['clicks'] ?? 0) 
                    : 0
            ];
            
            saveUrls($urls);
            $_SESSION['form_shortUrl'] = getBaseUrl() . $code;
            $_SESSION['form_createdCode'] = $code;
            $_SESSION['form_message'] = 'success';
        } else {
            $_SESSION['form_message'] = $msg;
        }
    } else {
        $_SESSION['form_message'] = 'invalid_url';
    }
    
    // Redirect to prevent resubmission
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cut It Off - URL Shortener</title>
	<link rel="manifest" href="manifest.json">
	<link rel="apple-touch-icon" href="apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="512x512" href="favicon.png"/>
	<link rel="shortcut icon" href="favicon.ico"/>
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">	
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <meta property="og:title" content="Cut It Off">
    <meta property="og:description" content="A lightweight, single-file URL shortener with API support, admin panel, and metadata management.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://mg.lu/cutitoff/">
    <meta property="og:image" content="https://mg.lu/cutitoff/favicon.png">
    <meta property="og:image:width" content="192">
    <meta property="og:image:height" content="192">
    <meta property="og:site_name" content="Cut It Off">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Cut It Off">
    <meta name="twitter:description" content="A lightweight, single-file URL shortener with API support, admin panel, and metadata management.">
    <meta name="twitter:image" content="https://mg.lu/cutitoff/favicon.png">
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Cut It Off - A lightweight, single-file URL shortener with API support, admin panel, and metadata management.">
    <meta name="keywords" content="cutitoff url save share shorten analyze">
    <meta name="author" content="Cut It Off">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://mg.lu/cutitoff/">
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Website",
        "name": "Cut It Off",
        "url": "https://mg.lu/cutitoff/",
        "sameAs": [
            "https://github.com/dayeggpi/cutitoff"
        ],
        "description": "A lightweight, single-file URL shortener with API support, admin panel, and metadata management."
    }
    </script>


	
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        :root {
            --bg: #0c0c10;
            --surface: #141419;
            --surface-2: #1c1c24;
            --accent: #7c3aed;
            --accent-light: #a78bfa;
            --accent-glow: rgba(124, 58, 237, 0.3);
            --success: #22c55e;
            --error: #ef4444;
            --warning: #f59e0b;
            --text: #fafafa;
            --text-dim: #a1a1aa;
            --text-muted: #71717a;
            --border: rgba(255,255,255,0.08);
        }
        
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Ambient background */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(ellipse at 20% 20%, rgba(124, 58, 237, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 80%, rgba(167, 139, 250, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

		/* Light theme */
		[data-theme="light"] {
			--bg: #f5f5f7;
			--surface: #ffffff;
			--surface-2: #e8e8ec;
			--accent: #7c3aed;
			--accent-light: #6d28d9;
			--accent-glow: rgba(124, 58, 237, 0.2);
			--success: #16a34a;
			--error: #dc2626;
			--warning: #d97706;
			--text: #1a1a1a;
			--text-dim: #4a4a4a;
			--text-muted: #6b6b6b;
			--border: rgba(0,0,0,0.1);
		}
		
		[data-theme="light"] body::before {
			background: 
				radial-gradient(ellipse at 20% 20%, rgba(124, 58, 237, 0.05) 0%, transparent 50%),
				radial-gradient(ellipse at 80% 80%, rgba(167, 139, 250, 0.03) 0%, transparent 50%);
		}
		
		[data-theme="light"] .card {
			box-shadow: 
				0 4px 24px rgba(0,0,0,0.08),
				0 0 0 1px rgba(0,0,0,0.05) inset;
		}
		
		/* Theme toggle button */
		.nav-actions {
			display: flex;
			justify-content: center;
			margin-bottom: 24px;
		}
		
		.icon-btn {
			width: 44px;
			height: 44px;
			border-radius: 12px;
			border: 1px solid var(--border);
			background: var(--surface);
			color: var(--text-dim);
			cursor: pointer;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 18px;
			transition: all 0.2s ease;
		}
		
		.icon-btn:hover {
			background: var(--surface-2);
			color: var(--accent-light);
			border-color: var(--accent);
		}
        
        .container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 540px;
        }
        
        .brand {
            text-align: center;
            margin-bottom: 48px;
        }
        
        .brand h1 {
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            margin-bottom: 8px;
        }
        
        .brand h1 span {
            background: linear-gradient(135deg, var(--accent-light), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .brand p {
            color: var(--text-muted);
            font-size: 1rem;
        }
        
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 36px;
            box-shadow: 
                0 4px 24px rgba(0,0,0,0.3),
                0 0 0 1px rgba(255,255,255,0.02) inset;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-dim);
            margin-bottom: 8px;
        }
        
        .form-group.optional label::after {
            content: 'optional';
            margin-left: 8px;
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 400;
        }
        
        input, textarea {
            width: 100%;
            padding: 16px 18px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            font-family: inherit;
            font-size: 15px;
            transition: all 0.2s ease;
        }
        
        input::placeholder, textarea::placeholder {
            color: var(--text-muted);
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px var(--accent-glow);
        }
        
        .input-url {
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
        }
        
        textarea {
            min-height: 70px;
            resize: vertical;
        }
        
        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            background: var(--surface-2);
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .checkbox-group:hover {
            background: rgba(124, 58, 237, 0.1);
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--accent);
            cursor: pointer;
        }
        
        .checkbox-group span {
            font-size: 13px;
            color: var(--text-dim);
        }
        
        .btn {
            width: 100%;
            padding: 16px 24px;
            background: linear-gradient(135deg, var(--accent) 0%, #9333ea 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-family: inherit;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(255,255,255,0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px var(--accent-glow);
        }
        
        .btn:hover::before {
            opacity: 1;
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .result {
            margin-top: 24px;
            padding: 24px;
            border-radius: 14px;
            animation: slideIn 0.4s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .result.success {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(34, 197, 94, 0.05));
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        
        .result.error {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .result.warning {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05));
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        
        .result-title {
            font-weight: 600;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .result.success .result-title { color: var(--success); }
        .result.error .result-title { color: var(--error); }
        .result.warning .result-title { color: var(--warning); }
        
        .short-url-box {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--surface-2);
            padding: 14px 18px;
            border-radius: 10px;
            margin-top: 12px;
        }
        
        .short-url-box a {
            flex: 1;
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            color: var(--accent-light);
            text-decoration: none;
            word-break: break-all;
        }
        
        .short-url-box a:hover {
            text-decoration: underline;
        }
        
        .copy-btn {
            padding: 10px 18px;
            background: var(--accent);
            border: none;
            border-radius: 8px;
            color: white;
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }
        
        .copy-btn:hover {
            background: var(--accent-light);
        }
        
        .toggle-advanced {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            background: transparent;
            border: 1px dashed var(--border);
            border-radius: 10px;
            color: var(--text-muted);
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .toggle-advanced:hover {
            border-color: var(--accent);
            color: var(--accent-light);
        }
        
        .advanced-options {
            display: none;
        }
        
        .advanced-options.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .footer {
            margin-top: 24px;
            text-align: center;
        }
        
        .footer a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13px;
            transition: color 0.2s;
        }
        
        .footer a:hover {
            color: var(--accent-light);
        }
        
        @media (max-width: 480px) {
            .row {
                grid-template-columns: 1fr;
            }
            
            .card {
                padding: 24px;
            }
            
            .brand h1 {
                font-size: 2rem;
            }
        }
		
		.qr-section {
			display: flex;
			flex-direction: column;
			align-items: center;
			gap: 16px;
			margin-top: 20px;
			padding-top: 20px;
			border-top: 1px solid var(--border);
		}

		#qrCanvas {
			background: white;
			padding: 16px;
			border-radius: 12px;
		}

		#qrCanvas svg {
			display: block;
		}

		.download-qr-btn {
			display: flex;
			align-items: center;
			gap: 8px;
			padding: 10px 20px;
			background: var(--surface-2);
			border: 1px solid var(--border);
			border-radius: 8px;
			color: var(--text-dim);
			font-family: inherit;
			font-size: 13px;
			font-weight: 500;
			cursor: pointer;
			transition: all 0.2s;
		}

		.download-qr-btn:hover {
			background: var(--accent);
			border-color: var(--accent);
			color: white;
		}		
    </style>
</head>
<body>
    <div class="container">
        <div class="brand">
            <h1>✂️ <span>Cut It Off</span></h1>
            <p>Transform long URLs into memorable links</p>
        </div>
		<div class="nav-actions">
    <button class="icon-btn" id="theme-toggle" title="Toggle theme">
        <i class="fas fa-sun"></i>
    </button>
</div>
        
        <div class="card">
            <form method="post">
                <div class="form-group">
                    <label for="url">Destination URL</label>
                    <input type="url" name="url" id="url" class="input-url" 
                           placeholder="https://example.com/your-long-url..." required>
                </div>
                
                <button type="button" class="toggle-advanced" onclick="toggleAdvanced()">
                    <span id="toggle-text">▸ Show advanced options</span>
                </button>
                
                <div class="advanced-options" id="advanced">
                    <div class="row">
                        <div class="form-group optional">
                            <label for="custom_code">Custom Short Code</label>
                            <input type="text" name="custom_code" id="custom_code" 
                                   placeholder="my-link" pattern="[a-zA-Z0-9_-]{1,50}">
                        </div>
                        <div class="form-group optional">
                            <label for="title">Title</label>
                            <input type="text" name="title" id="title" placeholder="My Link">
                        </div>
                    </div>
                    
                    <div class="form-group optional">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" 
                                  placeholder="A note to remember what this link is for..."></textarea>
                    </div>
                    
                    <label class="checkbox-group">
                        <input type="checkbox" name="allow_overwrite">
                        <span>Allow this short code to be overwritten later</span>
                    </label>
                </div>
                
				<button type="submit" class="btn">Shorten URL</button>
            </form>
						
			<?php if ($message === 'success'): ?>
			<div class="result success">
				<div class="result-title">✓ Link created successfully</div>
				<div class="short-url-box">
					<a href="<?= htmlspecialchars($shortUrl) ?>" target="_blank">
						<?= htmlspecialchars($shortUrl) ?>
					</a>
					<button class="copy-btn" onclick="copyUrl(this)">Copy</button>
				</div>
				<div class="qr-section">
					<div id="qrCanvas"></div>
					<button class="download-qr-btn" onclick="downloadQR()">
						<i class="fas fa-download"></i> Download QR code
					</button>
					⚠️ Do not forget to save this QR code if you need it
				</div>
			</div>
			<script>
				var generatedShortUrl = <?= json_encode($shortUrl) ?>;
			</script>
            <?php elseif ($message === 'invalid_url'): ?>
            <div class="result error">
                <div class="result-title">✕ Invalid URL</div>
                <p style="color:var(--text-dim);font-size:14px;">Please enter a valid URL including http:// or https://</p>
            </div>
            <?php elseif ($message === 'invalid_code'): ?>
            <div class="result error">
                <div class="result-title">✕ Invalid custom code</div>
                <p style="color:var(--text-dim);font-size:14px;">Use only letters, numbers, dashes and underscores (1-50 characters)</p>
            </div>
			<?php elseif ($message === 'code_protected'): ?>
			<div class="result warning">
				<div class="result-title">⚠ Code is protected</div>
				<p style="color:var(--text-dim);font-size:14px;">This short code already exists and was set to not allow overwriting. Please choose a different code.</p>
			</div>
			<?php elseif ($message === 'rate_limited'): ?>
			<div class="result error">
				<div class="result-title">✕ Too many requests</div>
				<p style="color:var(--text-dim);font-size:14px;">Please wait a while before creating more short URLs.</p>
			</div>
			<?php endif; ?>
        </div>
        <?php if($config['admin_show_link']) : ?>
        <div class="footer">
            <a href="<?php echo $config['admin_path'];?>">Admin Panel →</a>
        </div>
		<?php endif;?>
    </div>
    
    <script>
    
	<?php echo $QRcode_library;?>

     // Generate QR code on page load if URL exists
    if (typeof generatedShortUrl !== 'undefined' && generatedShortUrl) {
        const qrDiv = document.getElementById('qrCanvas');
        try {
            const qr = qrcode(0, 'L');
            qr.addData(generatedShortUrl, 'Byte');
            qr.make();
            qrDiv.innerHTML = qr.createSvgTag({ cellSize: 5, margin: 4 });
        } catch (e) {
            qrDiv.innerHTML = '<p style="color:var(--text-muted);">Could not generate QR code</p>';
        }
    }

    function downloadQR() {
        const qrDiv = document.getElementById('qrCanvas');
        const svg = qrDiv.querySelector('svg');
        if (!svg) return;
        
        const svgData = new XMLSerializer().serializeToString(svg);
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();
        
        img.onload = () => {
            canvas.width = img.width * 2;
            canvas.height = img.height * 2;
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            
            const link = document.createElement('a');
            link.download = 'qr-code.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        };
        
        img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
    }

    function toggleAdvanced() {
        const adv = document.getElementById('advanced');
        const text = document.getElementById('toggle-text');
        adv.classList.toggle('show');
        text.textContent = adv.classList.contains('show') 
            ? '▾ Hide advanced options' 
            : '▸ Show advanced options';
    }
    
    function copyUrl(btn) {
        const url = btn.previousElementSibling.textContent.trim();
        navigator.clipboard.writeText(url).then(() => {
            btn.textContent = 'Copied!';
            setTimeout(() => btn.textContent = 'Copy', 2000);
        });
    }
            
    // Theme toggle
    const themeToggle = document.getElementById('theme-toggle');
    const html = document.documentElement;

    const savedTheme = localStorage.getItem('theme') || 'dark';
    html.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);

    themeToggle.addEventListener('click', () => {
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
    });

    function updateThemeIcon(theme) {
        const icon = themeToggle.querySelector('i');
        icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }
		
	
    </script>
</body>
</html>
