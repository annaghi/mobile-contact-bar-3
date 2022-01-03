/*!
 * Mobile Contact Bar 3.0.0 by Anna Bansaghi - http://mobilecontactbar.com
 * License GPLv3 - https://www.gnu.org/licenses/gpl-3.0.en.html
 */
!function($,window,document){"use strict";$.fn.toggleAriaExpanded=function(){return this.attr("aria-expanded",function(index,attr){return"true"==attr?"false":"true"}),this},$.fn.initSettings=function(){var tbody=this,operators={"<":function(x,y){return Number(x)<Number(y)},"==":function(x,y){return x==y},"!=":function(x,y){return x!=y}};return tbody.children(".mcb-child").each(function(){var operand="."+$(this).classList().find(function(klass){return klass.startsWith("mcb-parent-")}),parent=tbody.children(".mcb-parent"+operand),value=parent.classList().find(function(klass){return klass.startsWith("mcb-trigger-")}).match(/^mcb-trigger-([<=!]+)(.*)$/),operator=value[1],operand=value[2]||"",value="";"true"===operand||!0===operand?(value=parent.find("input").toArray().reduce((acc,inputEl)=>acc||$(inputEl).getValue(),!1),operand=!0):value=""+parent.find("input").getValue(),operators[operator](value,operand)?$(this).fadeIn(500):$(this).fadeOut(500)}),tbody.children(".mcb-parent").each(function(){var self=$(this),match="."+self.classList().find(function(klass){return klass.startsWith("mcb-parent-")}),children=tbody.children(".mcb-child"+match),match=self.classList().find(function(klass){return klass.startsWith("mcb-trigger-")}).match(/^mcb-trigger-([<=!]+)(.*)$/),operator=match[1],operand=match[2]||"",value="";self.find("input, option").on("change input",function(){"true"===operand||!0===operand?(value=self.find("input").toArray().reduce((acc,inputEl)=>acc||$(inputEl).getValue(),$(this).getValue()),operand=!0):value=""+$(this).getValue(),operators[operator](value,operand)?children.each(function(){$(this).fadeIn(500)}):children.each(function(){$(this).fadeOut(500)})})}),this},$.fn.initSortableContacts=function(){return $(this).sortable({connectWith:"#mcb-contacts",handle:".mcb-sortable-draggable",items:".mcb-contact",start:function(event,ui){document.activeElement.blur(),$(this).find(".mcb-contact").removeClass("mcb-opened").end().find(".mcb-action-toggle-details").attr("aria-expanded","false"),ui.placeholder.height(ui.item.children(".mcb-summary").outerHeight()),ui.helper.height(ui.item.children(".mcb-summary").outerHeight()),ui.placeholder.css("visibility","visible"),$(this).sortable("refresh","refreshPositions")}}),this},$.fn.getValue=function(){var value="";switch(this.attr("type")){case"text":case"number":value=this.val();break;case"checkbox":value=this.prop("checked");break;case"radio":value=this.filter(":checked").val();break;case"select":value=this.filter(":selected").val()}return value},$.fn.classList=function(){return this[0].className.split(/\s+/)},$.fn.maxKey=function(rowType){var key=-1,attr="data-"+rowType+"-key";return 0===this.length||this.each(function(){key=Math.max(key,$(this).attr(attr))}),key},$.fn.blankIcon=function(){this.find('input[name$="[brand]"]').val("").end().find('input[name$="[group]"]').val("").end().find('input[name$="[icon]"]').val("").end().find(".mcb-summary-brand").addClass("mcb-blank-icon").text("--").end().find(".mcb-summary-icon").removeClass("mcb-fa").addClass("mcb-blank-icon").text("--").end().find(".mcb-details-brand").addClass("mcb-blank-icon").text("--").end().find(".mcb-details-icon").removeClass("mcb-fa").addClass("mcb-blank-icon").text("--")};function filtered_icons(icons,searchTerm){return""===searchTerm?icons:icons.filter(function(icon){return icon.includes(searchTerm)})}function circular_window_forward(iconList,path,icons,fn){var firstIconIndex=iconList.children().first().attr("data-icon"),firstIconIndex=icons.indexOf(firstIconIndex);fn(iconList,path,icons,firstIconIndex+30<icons.length?firstIconIndex+30:0)}function circular_window_backward(iconList,path,icons,fn){var prevPageFirstIconIndex=iconList.children().first().attr("data-icon"),firstIconIndex=icons.indexOf(prevPageFirstIconIndex),prevPageFirstIconIndex=0;0===firstIconIndex&&icons.length%30==0?prevPageFirstIconIndex=icons.length-30:0===firstIconIndex&&0<icons.length%30?prevPageFirstIconIndex=icons.length-icons.length%30:30<=firstIconIndex&&(prevPageFirstIconIndex=firstIconIndex-30),fn(iconList,path,icons,prevPageFirstIconIndex)}function ti_update_picker_window(iconList,path,icons,firstIconIndex){var icon;iconList.children().each(function(index){void 0===(icon=icons[firstIconIndex+index])?$(this).css({display:"none"}):($(this).css({display:"inline-block"}),$(this).attr("data-icon",icon),$(this).find("a").prop("title",icon),$(this).find("use").attr("xlink:href",path+"#tabler-"+icon))})}function fa_update_picker_window(iconList,path,icons,firstIconIndex){var icon,names;iconList.children().each(function(index){void 0===(icon=icons[firstIconIndex+index])?$(this).css({display:"none"}):(names=icon.split(" "),$(this).css({display:"inline-block"}),$(this).attr("data-icon",icon),$(this).find("a").prop("title",names[1]),$(this).find("use").attr("xlink:href",path+names[0]+".svg#"+names[1]))})}var option={init:function(){$("#mcb-section-bar tbody, #mcb-section-icons_labels tbody, #mcb-section-toggle tbody").initSettings(),option.contactList=$("#mcb-contacts"),option.contactList.initSortableContacts(),postboxes.add_postbox_toggles(pagenow),postboxes.pbhide=function(id){"mcb-meta-box-contacts"===id&&(document.activeElement.blur(),option.contactList.find(".mcb-contact").removeClass("mcb-opened").find(".mcb-action-toggle-details").attr("aria-expanded","false"))},option.onReady()},onReady:function(){var ti_icons=mobile_contact_bar.ti_icons,fa_icons=[];$.each(mobile_contact_bar.fa_icons,function(section,icons){$.each(icons,function(index,icon){fa_icons.push(section+" "+icon)})}),$(".mcb-settings").on("input change",".mcb-slider-input",function(){$(this).next("span").html(this.value+" "+$(this).data("postfix"))}),option.contactList.on("change",".mcb-summary-checkbox input",function(checked_contacts_length){checked_contacts_length.preventDefault(),checked_contacts_length.stopPropagation(),this.checked?$(this).closest(".mcb-contact").addClass("mcb-checked"):$(this).closest(".mcb-contact").removeClass("mcb-checked");checked_contacts_length=option.contactList.find(".mcb-checked").length;0===checked_contacts_length?$("#mcb-badge-length").removeClass().addClass("mcb-badge-disabled").text(0):$("#mcb-badge-length").removeClass().addClass("mcb-badge-enabled").text(checked_contacts_length)}),$("#mcb-bar-device").on("change","input",function(){"mcb-bar-device--none"===$(this).attr("id")?$("#mcb-badge-display").removeClass().addClass("mcb-badge-disabled").text(mobile_contact_bar.l10n.disabled):$("#mcb-badge-display").removeClass().addClass("mcb-badge-enabled").text(mobile_contact_bar.l10n.enabled)}),$("#mcb-add-contact").click(function(event){event.preventDefault(),event.stopPropagation();var contactKey=option.contactList.children(".mcb-contact").maxKey("contact")+1;$.ajax({url:ajaxurl,method:"POST",data:{action:"mcb_ajax_get_contact",nonce:mobile_contact_bar.nonce,contact_key:contactKey}}).done(function(contact){if(!contact)return!1;var data=JSON.parse(contact);if(!data.hasOwnProperty("summary")||!data.hasOwnProperty("details"))return!1;contact=document.createElement("div");$(contact).addClass(["mcb-contact","mcb-opened"]).attr("data-contact-key",contactKey),$(contact).append($(data.summary)).append($(data.details)),$(contact).find(".color-picker").wpColorPicker(),$(contact).find(".mcb-action-toggle-details").attr("aria-expanded","true"),option.contactList.append(contact)})}),option.contactList.on("click",".mcb-action-delete-contact",function(event){event.preventDefault(),event.stopPropagation(),$(this).closest(".mcb-contact").remove()}),option.contactList.on("click",".mcb-action-toggle-details",function(event){event.preventDefault(),event.stopPropagation(),$(this).toggleAriaExpanded().closest(".mcb-contact").toggleClass("mcb-opened")}),option.contactList.on("click",".mcb-action-close-details",function(event){event.preventDefault(),event.stopPropagation(),$(this).closest(".mcb-contact").removeClass("mcb-opened").find(".mcb-action-toggle-details").attr("aria-expanded","false"),document.getElementById("mcb-meta-box-contacts").scrollIntoView()}),option.contactList.on("click",".mcb-action-order-higher",function(contact){contact.preventDefault(),contact.stopPropagation();var focused=document.activeElement,prev=$(this).closest(".mcb-contact").prev(),contact=$(this).closest(".mcb-contact").detach();prev.before(contact),focused.focus()}),option.contactList.on("click",".mcb-action-order-lower",function(contact){contact.preventDefault(),contact.stopPropagation();var focused=document.activeElement,next=$(this).closest(".mcb-contact").next(),contact=$(this).closest(".mcb-contact").detach();next.after(contact),focused.focus()}),option.contactList.on("change",".mcb-details-type select",function(contactKey){contactKey.preventDefault(),contactKey.stopPropagation();var contact=$(this).closest(".mcb-contact"),contactKey=contact.attr("data-contact-key");$.ajax({url:ajaxurl,method:"POST",data:{action:"mcb_ajax_get_contact_field",nonce:mobile_contact_bar.nonce,contact_key:contactKey,contact_type:$(this).val()}}).done(function(data){if(!data)return!1;data=JSON.parse(data);if(!data.hasOwnProperty("contact_field")||!data.hasOwnProperty("uri")||!data.hasOwnProperty("parameters"))return!1;["scrolltotop"].includes(data.contact_field.type)?contact.find(".mcb-summary-uri").text("#"):contact.find(".mcb-summary-uri").text(data.contact_field.uri||mobile_contact_bar.l10n.no_URI),contact.find(".mcb-details-text input").val(data.contact_field.text),contact.find(".mcb-details-uri").replaceWith($(data.uri)),contact.find(".mcb-builtin-parameters, .mcb-link-parameters, .mcb-builtin-parameter, .mcb-link-parameter").detach(),contact.find(".mcb-details-uri").after($(data.parameters)),contact.find(".mcb-details-type .mcb-description").text(data.contact_field.desc_type)})}),option.contactList.on("click",".mcb-action-pick-icon",function(event){event.preventDefault(),event.stopPropagation(),setTimeout(function(){$("#mcb-icon-picker-container div input").focus()},100);var iconList,searchTerm,ti_path=mobile_contact_bar.plugin_url+"assets/svg/ti/tabler-sprite.svg",fa_path=mobile_contact_bar.plugin_url+"assets/svg/fa/sprites/",ti_filtered_icons=[],fa_filtered_icons=[],offset=$(this).offset(),contact=$(this).closest(".mcb-contact");$($.parseHTML($("#mcb-tmpl-icon-picker").html().replace(/\s{2,}/g,""))).css({top:offset.top-15,left:offset.left-(isRtl?185:0)}).appendTo("body").show(),iconList=$("#mcb-icon-picker-container ul"),fa_filtered_icons=filtered_icons(fa_icons,""),ti_filtered_icons=filtered_icons(ti_icons,""),$("body").off("click","#mcb-icon-picker-container button").on("click","#mcb-icon-picker-container button",function(brand){brand.preventDefault(),brand.stopPropagation();brand=$(this).attr("data-brand");$("#mcb-icon-picker-container").find("button").removeClass("mcb-brand-active"),$(this).addClass("mcb-brand-active"),"ti"===brand?ti_update_picker_window(iconList,ti_path,ti_filtered_icons,0):("fa"===brand||($("#mcb-icon-picker-container").find("button").removeClass("mcb-brand-active"),$("#mcb-icon-picker-container").find('button[data-brand="fa"]').addClass("mcb-brand-active")),fa_update_picker_window(iconList,fa_path,fa_filtered_icons,0))}),$("body").off("click","#mcb-icon-picker-container ul li a").on("click","#mcb-icon-picker-container ul li a",function(icon){icon.preventDefault(),icon.stopPropagation();var brand=$("#mcb-icon-picker-container").find("button.mcb-brand-active").attr("data-brand"),icon=$(this).closest("li").attr("data-icon"),names="fa"===brand?icon.split(" "):["",icon];if($("#mcb-icon-picker-container").remove(),!["ti","fa"].includes(brand)||"ti"===brand&&!ti_icons.includes(icon)||"fa"===brand&&!fa_icons.includes(icon))return contact.blankIcon(),!1;$.ajax({url:ajaxurl,method:"POST",data:{action:"mcb_ajax_get_icon",nonce:mobile_contact_bar.nonce,brand:brand,group:names[0],icon:names[1]}}).done(function(svg){if(!svg)return contact.blankIcon(),!1;svg=JSON.parse(svg);if(svg.length<=0)return contact.blankIcon(),!1;contact.find('input[name$="[brand]"]').val(brand).end().find('input[name$="[group]"]').val(names[0]).end().find('input[name$="[icon]"]').val(names[1]).end().find(".mcb-summary-brand").removeClass("mcb-blank-icon").text(brand.toUpperCase()).end().find(".mcb-summary-icon").removeClass(["mcb-blank-icon","mcb-fa"]).empty().append(svg).end().find(".mcb-details-brand").removeClass("mcb-blank-icon").text(brand.toUpperCase()).end().find(".mcb-details-icon").removeClass(["mcb-blank-icon","mcb-fa"]).empty().append(svg),"fa"===brand&&contact.find(".mcb-summary-icon").addClass("mcb-fa").end().find(".mcb-details-icon").addClass("mcb-fa")})}),$("body").off("click","#mcb-icon-picker-container div a").on("click","#mcb-icon-picker-container div a",function(brand){brand.preventDefault(),brand.stopPropagation();brand=$("#mcb-icon-picker-container").find("button.mcb-brand-active").attr("data-brand");"ti"===brand?("back"===$(this).attr("data-direction")?circular_window_backward:circular_window_forward)(iconList,ti_path,ti_filtered_icons,ti_update_picker_window):"fa"===brand?("back"===$(this).attr("data-direction")?circular_window_backward:circular_window_forward)(iconList,fa_path,fa_filtered_icons,fa_update_picker_window):($("#mcb-icon-picker-container").find("button").removeClass("mcb-brand-active"),$("#mcb-icon-picker-container").find('button[data-brand="fa"]').addClass("mcb-brand-active"),fa_update_picker_window(iconList,fa_path,fa_filtered_icons,0))}),$("body").off("input","#mcb-icon-picker-container div input").on("input","#mcb-icon-picker-container div input",function(brand){brand.preventDefault(),brand.stopPropagation();brand=$("#mcb-icon-picker-container").find("button.mcb-brand-active").attr("data-brand");searchTerm=$(this).val(),ti_filtered_icons=filtered_icons(ti_icons,searchTerm),fa_filtered_icons=filtered_icons(fa_icons,searchTerm),"ti"===brand?ti_update_picker_window(iconList,ti_path,ti_filtered_icons,0):("fa"===brand||($("#mcb-icon-picker-container").find("button").removeClass("mcb-brand-active"),$("#mcb-icon-picker-container").find('button[data-brand="fa"]').addClass("mcb-brand-active")),fa_update_picker_window(iconList,fa_path,fa_filtered_icons,0))}),$(document).off("mouseup").on("mouseup",function(event){event.preventDefault(),event.stopPropagation(),$("#mcb-icon-picker-container").is(event.target)||0!==$("#mcb-icon-picker-container").has(event.target).length||$("#mcb-icon-picker-container").remove()}),$(window).resize(function(){$("#mcb-icon-picker-container").is(event.target)||0!==$("#mcb-icon-picker-container").has(event.target).length||$("#mcb-icon-picker-container").remove()})}),option.contactList.on("click",".mcb-action-clear-icon",function(event){event.preventDefault(),event.stopPropagation(),$(this).closest(".mcb-contact").blankIcon()}),option.contactList.on("input",".mcb-details-label input",function(event){event.preventDefault(),event.stopPropagation(),$(this).closest(".mcb-contact").find(".mcb-summary-label").text($(this).val())}),option.contactList.on("input",".mcb-details-uri input",function(contact){contact.preventDefault(),contact.stopPropagation();var uri=$(this).val(),contact=$(this).closest(".mcb-contact");""===uri?contact.find(".mcb-summary-uri").removeClass("mcb-monospace").text(mobile_contact_bar.l10n.no_URI):contact.find(".mcb-summary-uri").addClass("mcb-monospace").text(uri)}),option.contactList.on("click",".mcb-action-add-parameter",function(contactKey){contactKey.preventDefault(),contactKey.stopPropagation();var contact=$(this).closest(".mcb-contact"),parameterKey=contact.find(".mcb-link-parameter"),contactKey=contact.attr("data-contact-key"),parameterKey=parameterKey.maxKey("parameter")+1;$.ajax({url:ajaxurl,method:"POST",data:{action:"mcb_ajax_get_parameter",nonce:mobile_contact_bar.nonce,contact_key:contactKey,parameter_key:parameterKey}}).done(function(parameter){if(!parameter)return!1;parameter=JSON.parse(parameter);if(parameter.length<=0)return!1;contact.find(".mcb-link-parameters").after($(parameter))})}),option.contactList.on("click",".mcb-action-delete-parameter",function(event){event.preventDefault(),event.stopPropagation(),$(this).closest(".mcb-link-parameter").remove()})}};$(document).ready(option.init)}(jQuery,window,document);!function($){var backgroundImage,wpColorPickerAlpha={version:300};if("wpColorPickerAlpha"in window&&"version"in window.wpColorPickerAlpha){var version=parseInt(window.wpColorPickerAlpha.version,10);if(!isNaN(version)&&wpColorPickerAlpha.version<=version)return}Color.fn.hasOwnProperty("to_s")||(Color.fn.to_s=function(type){var color="";return"hex"===(type="hex"===(type=type||"hex")&&this._alpha<1?"rgba":type)?color=this.toString():this.error||(color=this.toCSS(type).replace(/\(\s+/,"(").replace(/\s+\)/,")")),color},window.wpColorPickerAlpha=wpColorPickerAlpha,$.widget("a8c.iris",$.a8c.iris,{alphaOptions:{alphaEnabled:!(backgroundImage="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAIAAAHnlligAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAHJJREFUeNpi+P///4EDBxiAGMgCCCAGFB5AADGCRBgYDh48CCRZIJS9vT2QBAggFBkmBiSAogxFBiCAoHogAKIKAlBUYTELAiAmEtABEECk20G6BOmuIl0CIMBQ/IEMkO0myiSSraaaBhZcbkUOs0HuBwDplz5uFJ3Z4gAAAABJRU5ErkJggg==")},_getColor:function(color){return void 0===color&&(color=this._color),this.alphaOptions.alphaEnabled?(color=color.to_s(this.alphaOptions.alphaColorType),this.alphaOptions.alphaColorWithSpace?color:color.replace(/\s+/g,"")):color.toString()},_create:function(){try{this.alphaOptions=this.element.wpColorPicker("instance").alphaOptions}catch(e){}$.extend({},this.alphaOptions,{alphaEnabled:!1,alphaCustomWidth:130,alphaReset:!1,alphaColorType:"hex",alphaColorWithSpace:!1}),this._super()},_addInputListeners:function(input){function callback(event){var val=input.val(),color=new Color(val),val=val.replace(/^(#|(rgb|hsl)a?)/,""),type=self.alphaOptions.alphaColorType;input.removeClass("iris-error"),color.error?""!==val&&input.addClass("iris-error"):"hex"===type&&"keyup"===event.type&&val.match(/^[0-9a-fA-F]{3}$/)||color.toIEOctoHex()!==self._color.toIEOctoHex()&&self._setOption("color",self._getColor(color))}var self=this;input.on("change",callback).on("keyup",self._debounce(callback,100)),self.options.hide&&input.one("focus",function(){self.show()})},_initControls:function(){var self,stripAlpha,stripAlphaSlider,controls;this._super(),this.alphaOptions.alphaEnabled&&(stripAlphaSlider=(stripAlpha=(self=this).controls.strip.clone(!1,!1)).find(".iris-slider-offset"),controls={stripAlpha:stripAlpha,stripAlphaSlider:stripAlphaSlider},stripAlpha.addClass("iris-strip-alpha"),stripAlphaSlider.addClass("iris-slider-offset-alpha"),stripAlpha.appendTo(self.picker.find(".iris-picker-inner")),$.each(controls,function(k,v){self.controls[k]=v}),self.controls.stripAlphaSlider.slider({orientation:"vertical",min:0,max:100,step:1,value:parseInt(100*self._color._alpha),slide:function(event,ui){self.active="strip",self._color._alpha=parseFloat(ui.value/100),self._change.apply(self,arguments)}}))},_dimensions:function(strip){if(this._super(strip),this.alphaOptions.alphaEnabled){var opts=this.options,square=this.controls.square,strip=this.picker.find(".iris-strip"),innerWidth=Math.round(this.picker.outerWidth(!0)-(opts.border?22:0)),squareWidth=Math.round(square.outerWidth()),stripWidth=Math.round((innerWidth-squareWidth)/2),stripMargin=Math.round(stripWidth/2),totalWidth=Math.round(squareWidth+2*stripWidth+2*stripMargin);while(innerWidth<totalWidth)stripWidth=Math.round(stripWidth-2),stripMargin=Math.round(stripMargin-1),totalWidth=Math.round(squareWidth+2*stripWidth+2*stripMargin);square.css("margin","0"),strip.width(stripWidth).css("margin-left",stripMargin+"px")}},_change:function(){var controls,alpha,gradient,self=this,active=self.active;self._super(),self.alphaOptions.alphaEnabled&&(controls=self.controls,alpha=parseInt(100*self._color._alpha),gradient=["rgb("+(gradient=self._color.toRgb()).r+","+gradient.g+","+gradient.b+") 0%","rgba("+gradient.r+","+gradient.g+","+gradient.b+", 0) 100%"],self.picker.closest(".wp-picker-container").find(".wp-color-result"),self.options.color=self._getColor(),controls.stripAlpha.css({background:"linear-gradient(to bottom, "+gradient.join(", ")+"), url("+backgroundImage+")"}),active&&controls.stripAlphaSlider.slider("value",alpha),self._color.error||self.element.removeClass("iris-error").val(self.options.color),self.picker.find(".iris-palette-container").on("click.palette",".iris-palette",function(){var color=$(this).data("color");self.alphaOptions.alphaReset&&(self._color._alpha=1,color=self._getColor()),self._setOption("color",color)}))},_paintDimension:function(origin,control){var color=!1;this.alphaOptions.alphaEnabled&&"strip"===control&&(color=this._color,this._color=new Color(color.toString()),this.hue=this._color.h()),this._super(origin,control),color&&(this._color=color)},_setOption:function(key,value){if("color"!==key||!this.alphaOptions.alphaEnabled)return this._super(key,value);value=""+value,newColor=new Color(value).setHSpace(this.options.mode),newColor.error||this._getColor(newColor)===this._getColor()||(this._color=newColor,this.options.color=this._getColor(),this.active="external",this._change())},color:function(newColor){return!0===newColor?this._color.clone():void 0===newColor?this._getColor():void this.option("color",newColor)}}),$.widget("wp.wpColorPicker",$.wp.wpColorPicker,{alphaOptions:{alphaEnabled:!1},_getAlphaOptions:function(){var el=this.element,type=el.data("type")||this.options.type,color=el.data("defaultColor")||el.val(),options={alphaEnabled:el.data("alphaEnabled")||!1,alphaCustomWidth:130,alphaReset:!1,alphaColorType:"rgb",alphaColorWithSpace:!1};return options.alphaEnabled&&(options.alphaEnabled=el.is("input")&&"full"===type),options.alphaEnabled&&(options.alphaColorWithSpace=color&&color.match(/\s/),$.each(options,function(name,defaultValue){var value=el.data(name)||defaultValue;switch(name){case"alphaCustomWidth":value=value?parseInt(value,10):0,value=isNaN(value)?defaultValue:value;break;case"alphaColorType":value.match(/^(hex|(rgb|hsl)a?)$/)||(value=color&&color.match(/^#/)?"hex":color&&color.match(/^hsla?/)?"hsl":defaultValue);break;default:value=!!value}options[name]=value})),options},_create:function(){$.support.iris&&(this.alphaOptions=this._getAlphaOptions(),this._super())},_addListeners:function(){if(!this.alphaOptions.alphaEnabled)return this._super();var self=this,el=self.element,isDeprecated=self.toggler.is("a"),id=self.element.attr("id");self.element.attr("id",null),self.toggler.attr("id",id),this.alphaOptions.defaultWidth=el.width(),this.alphaOptions.alphaCustomWidth&&el.width(parseInt(this.alphaOptions.defaultWidth+this.alphaOptions.alphaCustomWidth,10)),self.toggler.css({position:"relative","background-image":"url("+backgroundImage+")"}),isDeprecated?self.toggler.html('<span class="color-alpha" />'):self.toggler.append('<span class="color-alpha" />'),self.colorAlpha=self.toggler.find("span.color-alpha").css({width:"30px",height:"100%",position:"absolute",top:0,"background-color":el.val()}),isRtl?self.colorAlpha.css({"border-bottom-right-radius":"2px","border-top-right-radius":"2px",right:0}):self.colorAlpha.css({"border-bottom-left-radius":"2px","border-top-left-radius":"2px",left:0}),el.iris({change:function(event,ui){self.colorAlpha.css({"background-color":ui.color.to_s(self.alphaOptions.alphaColorType)}),$.isFunction(self.options.change)&&self.options.change.call(this,event,ui)}}),self.wrap.on("click.wpcolorpicker",function(event){event.stopPropagation()}),self.toggler.on("click",function(){self.toggler.hasClass("wp-picker-open")?self.close():self.open()}),el.change(function(event){var val=$(this).val();(el.hasClass("iris-error")||""===val||val.match(/^(#|(rgb|hsl)a?)$/))&&(isDeprecated&&self.toggler.removeAttr("style"),self.colorAlpha.css("background-color",""),$.isFunction(self.options.clear)&&self.options.clear.call(this,event))}),self.button.on("click",function(event){$(this).hasClass("wp-picker-default")?el.val(self.options.defaultColor).change():$(this).hasClass("wp-picker-clear")&&(el.val(""),isDeprecated&&self.toggler.removeAttr("style"),self.colorAlpha.css("background-color",""),$.isFunction(self.options.clear)&&self.options.clear.call(this,event),el.trigger("change"))})}}))}(jQuery),jQuery(function(){jQuery(".color-picker").wpColorPicker()});