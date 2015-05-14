/**
 * IPS Social Suite 4
 * (c) 2013 Invision Power Services - http://www.invisionpower.com
 * This file contains minified javascript and is not directly editable
 */

;(function($,_,undefined){"use strict";ips.controller.register('forums.front.topic.answers',{ajaxObj:null,initialize:function(){this.on('click','a.cAnswerRate',this.rate);},rate:function(e){e.preventDefault();var self=this;var clicked=$(e.currentTarget);var positive=clicked.hasClass('cAnswerRate_up');var voteCount=this.scope.find('[data-role="voteCount"]');var currentVotes=parseInt(voteCount.attr('data-voteCount'));this.scope.find('.cAnswerRate_up').toggleClass('ipsType_positive',positive);this.scope.find('.cAnswerRate_down').toggleClass('ipsType_negative',!positive);this.scope.toggleClass('cRatingColumn_up',positive).toggleClass('cRatingColumn_down',!positive);var newVoteCount=0;if(positive){if(currentVotes===-1){newVoteCount=1;}else{newVoteCount=currentVotes+1;}}else{if(currentVotes===1){newVoteCount=-1;}else{newVoteCount=currentVotes-1;}}
voteCount.toggleClass('ipsType_positive',positive).toggleClass('ipsType_negative',!positive).text(newVoteCount).attr('data-voteCount',newVoteCount);if(this.ajaxObj&&_.isFunction(this.ajaxObj.abort)){this.ajaxObj.abort();}
if(positive){this.scope.find('a.cAnswerRate_up').addClass('ipsHide');this.scope.find('span.cAnswerRate_up').removeClass('ipsHide');}else{this.scope.find('a.cAnswerRate_down').addClass('ipsHide');this.scope.find('span.cAnswerRate_down').removeClass('ipsHide');}
this.ajaxObj=ips.getAjax()(clicked.attr('href')).done(function(response){Debug.log(response);if(!response.canVoteUp){self.scope.find('a.cAnswerRate_up').addClass('ipsHide');self.scope.find('span.cAnswerRate_up').removeClass('ipsHide');}else{self.scope.find('a.cAnswerRate_up').removeClass('ipsHide');self.scope.find('span.cAnswerRate_up').addClass('ipsHide');}
if(!response.canVoteDown){self.scope.find('a.cAnswerRate_down').addClass('ipsHide');self.scope.find('span.cAnswerRate_down').removeClass('ipsHide');}else{self.scope.find('a.cAnswerRate_down').removeClass('ipsHide');self.scope.find('span.cAnswerRate_down').addClass('ipsHide');}
voteCount.text(response.votes);self.scope.find('.ipsType_light').text(ips.pluralize(ips.getString('votes_no_number'),response.votes));});}});}(jQuery,_));;
;(function($,_,undefined){"use strict";ips.controller.register('forums.front.topic.reply',{initialize:function(){this.on('click','[data-action="replyToTopic"]',this.replyToTopic);},replyToTopic:function(e){e.preventDefault();$(document).trigger('replyToTopic');}});}(jQuery,_));;
;(function($,_,undefined){"use strict";ips.controller.register('forums.front.topic.view',{initialize:function(){$(document).on('replyToTopic',_.bind(this.replyToTopic,this));},replyToTopic:function(e){var editorID=this.scope.find('[data-role="replyArea"] [data-role="contentEditor"]').attr('name');if(editorID){this.trigger('initializeEditor',{editorID:editorID});}}});}(jQuery,_));;