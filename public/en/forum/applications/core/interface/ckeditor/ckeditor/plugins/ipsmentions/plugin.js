﻿CKEDITOR.plugins.add("ipsmentions",{init:function(d){var h=!1,a=null,j=null,c=null;d.on("key",function(e){if(h){if(40==e.data.keyCode){var b=a.children("[data-selected]");b.length?(b.removeAttr("data-selected"),b.next().attr("data-selected",!0)):a.children(":first-child").attr("data-selected",!0);e.cancel();return}if(38==e.data.keyCode){b=a.children("[data-selected]");b.length?(b.removeAttr("data-selected"),b.prev().attr("data-selected",!0)):a.children(":last-child").attr("data-selected",!0);e.cancel();
return}if(13==e.data.keyCode){b=a.children("[data-selected]");b.length&&b.click();e.cancel();return}}setTimeout(function(){var b=d.getSelection().getRanges()[0];if(b){var i=b.startContainer.getText(),g=b.startContainer.getPrevious();CKEDITOR.env.ie&&g&&(g=g.getPrevious());if(h)if(27==e.data.keyCode||null===g)a.remove(),a=null,h=!1,c&&_.isFunction(c.abort)&&c.abort();else{a.show();if(c&&_.isFunction(c.abort))try{c.abort()}catch(n){}clearTimeout(j);j=setTimeout(function(){c=ips.getAjax()(ips.getSetting("baseURL")+
"?app=core&module=system&controller=editor&do=mention&input="+encodeURIComponent(i).replace("%E2%80%8B","")).done(function(f){f&&a?(a.removeClass("ipsLoading"),a.html(f),a.children().click(function(){g.setText(g.getText().substr(0,g.getText().length-1));var c=d.document.createElement("a");c.setAttribute("href",$(this).attr("data-mentionhref"));c.setAttribute("data-ipsHover","");c.setAttribute("data-ipsHover-target",$(this).attr("data-mentionhover"));c.setAttribute("data-mentionid",$(this).attr("data-mentionid"));
c.setHtml("@"+$(this).find('[data-role="mentionname"]').html());c.replace(b.startContainer);a.remove();a=null;h=!1;d.focus()})):32==e.data.keyCode?(a.remove(),a=null,h=!1,c&&_.isFunction(c.abort)&&c.abort()):a&&a.hide()})},500)}else if("@"==i[b.startOffset-1]&&(1==b.startOffset||i.substr(b.startOffset-2,1).match(/\s{1}/i))){var f=d.document.createElement("span");f.setAttribute("data-caret","true");CKEDITOR.env.ie?f.appendHtml("&zwnj;"):f.appendHtml("&nbsp;");d.insertElement(f);a=$('<ul class="ipsMenu ipsMenu_auto ipsMenu_bottomLeft ipsLoading" data-mentionMenu></ul>').hide();
$("body").append(a);var k=$("#cke_"+d.name).find("iframe").offset(),m=$("#cke_"+d.name).find("iframe").contents().find("body").offset(),l=$("#cke_"+d.name).find("iframe").contents().find("span[data-caret]").offset();a.css({top:k.top+m.top+l.top,left:k.left+l.left-30});CKEDITOR.env.ie||f.remove();h=!0}}},5)})}});