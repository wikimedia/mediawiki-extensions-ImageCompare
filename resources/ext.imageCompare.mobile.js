function initComparisons(){var e,t;for(e=document.getElementsByClassName("img-comp-overlay"),t=0;t<e.length;t++)n(e[t]);function n(e){var t,n,o,i=0;function s(e){e.preventDefault(),i=1,window.addEventListener("mousemove",f),window.addEventListener("touchmove",f)}function d(){i=0}function f(o){var s,d;if(0==i)return!1;(s=o.changedTouches[0].clientX)<0&&(s=0),s>n&&(s=n),d=s,e.style.width=d+"px",t.style.left=e.offsetWidth-t.offsetWidth/2+"px"}n=e.offsetWidth,o=e.offsetHeight,e.style.width=n/2+"px",(t=document.createElement("DIV")).setAttribute("class","img-comp-slider"),e.parentElement.insertBefore(t,e),t.style.top=o/2-t.offsetHeight/2+"px",t.style.left=n/2-t.offsetWidth/2+"px",t.addEventListener("mousedown",s),window.addEventListener("mouseup",d),t.addEventListener("touchstart",s),window.addEventListener("touchstop",d)}}initComparisons();