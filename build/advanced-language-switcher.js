!function(){"use strict";var e,t={915:function(){const e="Session";document.addEventListener("DOMContentLoaded",(function(){const t=document.querySelector(".wp-site-blocks"),n=document.getElementById("masthead")||t.querySelector("header"),o=document.getElementById("overlay-wrapper"),l=document.getElementById("pls-language-switcher"),r=document.getElementById("pls-modal-selector"),s=document.getElementById("pls-language-select"),i=document.getElementById("pls-region-select"),c=document.getElementById("pls-button-submit"),a=r.querySelector(".pls-button-close");function d(e=!0){window.tempScrollTop||(window.tempScrollTop=window.pageYOffset),e?(n.classList.add("pls-no-animations"),document.body.classList.add("pls-no-scroll"),t.style.transform=`translateY(-${window.tempScrollTop}px)`,n.style.transform=`translateY(${window.tempScrollTop}px)`):(n.classList.remove("pls-no-animations"),document.body.classList.remove("pls-no-scroll"),t.style.transform=null,n.style.transform=null,window.scrollTo({top:window.tempScrollTop}),window.tempScrollTop=0)}function u(){o.style.display="none",r.style.display="none",d(!1)}l.addEventListener("click",(()=>{o.style.display="flex",d(),r.style.display="block"})),o.addEventListener("click",(()=>{u()})),a.addEventListener("click",(()=>{u()})),c.addEventListener("click",(t=>{t.preventDefault();const n=s.options[s.selectedIndex].value,o=s.options[s.selectedIndex].title,l=i.options[i.selectedIndex].value,r=wp.cookiePath,c=wp.cookieDomain;function a(e,t,n){let o="";if(n){const e=new Date;e.setTime(e.getTime()+24*n*60*60*1e3),o="; expires="+e.toGMTString()}else o="; ";document.cookie=`${e}=${t}${o}; path=${r}; domain=${c}`}function d(e){a(e,"",-1)}d("pll_language"),a("pll_language",n,e),d("brb_region"),a("brb_region",l,e),document.location.href=o}))}))}},n={};function o(e){var l=n[e];if(void 0!==l)return l.exports;var r=n[e]={exports:{}};return t[e](r,r.exports,o),r.exports}o.m=t,e=[],o.O=function(t,n,l,r){if(!n){var s=1/0;for(d=0;d<e.length;d++){n=e[d][0],l=e[d][1],r=e[d][2];for(var i=!0,c=0;c<n.length;c++)(!1&r||s>=r)&&Object.keys(o.O).every((function(e){return o.O[e](n[c])}))?n.splice(c--,1):(i=!1,r<s&&(s=r));if(i){e.splice(d--,1);var a=l();void 0!==a&&(t=a)}}return t}r=r||0;for(var d=e.length;d>0&&e[d-1][2]>r;d--)e[d]=e[d-1];e[d]=[n,l,r]},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},function(){var e={943:0,98:0};o.O.j=function(t){return 0===e[t]};var t=function(t,n){var l,r,s=n[0],i=n[1],c=n[2],a=0;if(s.some((function(t){return 0!==e[t]}))){for(l in i)o.o(i,l)&&(o.m[l]=i[l]);if(c)var d=c(o)}for(t&&t(n);a<s.length;a++)r=s[a],o.o(e,r)&&e[r]&&e[r][0](),e[r]=0;return o.O(d)},n=self.webpackChunkpolylang_language_switcher=self.webpackChunkpolylang_language_switcher||[];n.forEach(t.bind(null,0)),n.push=t.bind(null,n.push.bind(n))}();var l=o.O(void 0,[98],(function(){return o(915)}));l=o.O(l)}();