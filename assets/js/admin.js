/*!
 * Mobile Contact Bar 3.0.0 by Anna Bansaghi - https://mobilecontactbar.com
 * License GPLv3 - https://www.gnu.org/licenses/gpl-3.0.en.html
 */
!function($,window,document){"use strict";function filtered_icons(icons,searchTerm){return""===searchTerm?icons:icons.filter(function(icon){return icon.includes(searchTerm)})}function circular_window_forward(iconList,path,icons,fn){var firstIconIndex=iconList.children().first().attr("data-icon"),firstIconIndex=icons.indexOf(firstIconIndex);fn(iconList,path,icons,firstIconIndex+30<icons.length?firstIconIndex+30:0)}function circular_window_backward(iconList,path,icons,fn){var prevPageFirstIconIndex=iconList.children().first().attr("data-icon"),firstIconIndex=icons.indexOf(prevPageFirstIconIndex),prevPageFirstIconIndex=0;0===firstIconIndex&&icons.length%30==0?prevPageFirstIconIndex=icons.length-30:0===firstIconIndex&&0<icons.length%30?prevPageFirstIconIndex=icons.length-icons.length%30:30<=firstIconIndex&&(prevPageFirstIconIndex=firstIconIndex-30),fn(iconList,path,icons,prevPageFirstIconIndex)}function ti_update_picker_window(iconList,path,icons,firstIconIndex){var icon;iconList.children().each(function(index){void 0===(icon=icons[firstIconIndex+index])?$(this).css({display:"none"}):($(this).css({display:"inline-block"}),$(this).attr("data-icon",icon),$(this).find("a").prop("title",icon),$(this).find("use").attr("xlink:href",path+"#tabler-"+icon))})}function fa_update_picker_window(iconList,path,icons,firstIconIndex){var icon,names;iconList.children().each(function(index){void 0===(icon=icons[firstIconIndex+index])?$(this).css({display:"none"}):(names=icon.split(" "),$(this).css({display:"inline-block"}),$(this).attr("data-icon",icon),$(this).find("a").prop("title",names[1]),$(this).find("use").attr("xlink:href",path+names[0]+".svg#"+names[1]))})}$.fn.toggleAriaExpanded=function(){return this.attr("aria-expanded",function(index,attr){return"true"==attr?"false":"true"}),this},$.fn.initSettings=function(){var tbody=this,operators={"<":function(x,y){return Number(x)<Number(y)},"==":function(x,y){return x==y},"!=":function(x,y){return x!=y}};return tbody.children(".mcb-child").each(function(){var operand="."+$(this).classList().find(function(klass){return klass.startsWith("mcb-parent-")}),parent=tbody.children(".mcb-parent"+operand),value=parent.classList().find(function(klass){return klass.startsWith("mcb-trigger-")}).match(/^mcb-trigger-([<=!]+)(.*)$/),operator=value[1],operand=value[2]||"",value="";"true"===operand||!0===operand?(value=parent.find("input").toArray().reduce((acc,inputEl)=>acc||$(inputEl).getValue(),!1),operand=!0):value=""+parent.find("input").getValue(),operators[operator](value,operand)?$(this).fadeIn(500):$(this).fadeOut(500)}),tbody.children(".mcb-parent").each(function(){var self=$(this),match="."+self.classList().find(function(klass){return klass.startsWith("mcb-parent-")}),children=tbody.children(".mcb-child"+match),match=self.classList().find(function(klass){return klass.startsWith("mcb-trigger-")}).match(/^mcb-trigger-([<=!]+)(.*)$/),operator=match[1],operand=match[2]||"",value="";self.find("input, option").on("change input",function(){"true"===operand||!0===operand?(value=self.find("input").toArray().reduce((acc,inputEl)=>acc||$(inputEl).getValue(),$(this).getValue()),operand=!0):value=""+$(this).getValue(),operators[operator](value,operand)?children.each(function(){$(this).fadeIn(500)}):children.each(function(){$(this).fadeOut(500)})})}),this},$.fn.initSortableButtons=function(){return $(this).sortable({connectWith:"#mcb-builder",handle:".mcb-sortable-draggable",items:".mcb-button",start:function(event,ui){document.activeElement.blur(),$(this).find(".mcb-button").removeClass("mcb-opened").end().find(".mcb-action-toggle-details").attr("aria-expanded","false"),ui.placeholder.height(ui.item.children(".mcb-summary").outerHeight()),ui.helper.height(ui.item.children(".mcb-summary").outerHeight()),ui.placeholder.css("visibility","visible"),$(this).sortable("refresh","refreshPositions")}}),this},$.fn.getValue=function(){var value="";switch(this.attr("type")){case"text":case"number":value=this.val();break;case"checkbox":value=this.prop("checked");break;case"radio":value=this.filter(":checked").val();break;case"select":value=this.filter(":selected").val()}return value},$.fn.classList=function(){return this[0].className.split(/\s+/)},$.fn.maxKey=function(rowType){var key=-1,attr="data-"+rowType+"-key";return 0===this.length||this.each(function(){key=Math.max(key,$(this).attr(attr))}),key},$.fn.blankIcon=function(){this.find('input[name$="[brand]"]').val("").end().find('input[name$="[group]"]').val("").end().find('input[name$="[icon]"]').val("").end().find(".mcb-summary-brand").addClass("mcb-blank-icon").text("--").end().find(".mcb-summary-icon").removeClass("mcb-fa").addClass("mcb-blank-icon").text("--").end().find(".mcb-details-brand").addClass("mcb-blank-icon").text("--").end().find(".mcb-details-icon").removeClass("mcb-fa").addClass("mcb-blank-icon").text("--")},$.fn.loadingIcon=function(){this.find('input[name$="[brand]"]').val("").end().find('input[name$="[group]"]').val("").end().find('input[name$="[icon]"]').val("").end().find(".mcb-summary-brand").addClass("mcb-blank-icon").text("--").end().find(".mcb-summary-icon").removeClass("mcb-blank-icon").addClass("mcb-loading-icon").empty().end().find(".mcb-details-brand").addClass("mcb-blank-icon").text("--").end().find(".mcb-details-icon").removeClass("mcb-blank-icon").addClass("mcb-loading-icon").empty()};var option={init:function(){option.settings=$("#mcb-section-bar tbody, #mcb-section-buttons tbody, #mcb-section-toggle tbody, #mcb-section-badge tbody"),option.settings.initSettings(),option.builder=$("#mcb-builder"),option.builder.initSortableButtons(),postboxes.add_postbox_toggles(pagenow),postboxes.pbhide=function(id){"mcb-meta-box-builder"===id&&(document.activeElement.blur(),option.builder.find(".mcb-button").removeClass("mcb-opened").find(".mcb-action-toggle-details").attr("aria-expanded","false"))},option.ti_icons=mcb.ti_icons,option.fa_icons=[],$.each(mcb.fa_icons,function(section,icons){$.each(icons,function(index,icon){option.fa_icons.push(section+" "+icon)})}),option.onReady()},onReady:function(){$("#mcb-form").submit(function(){$("#submit").addClass("mcb-loading")}),option.builder.on("change",".mcb-summary-checkbox input",function(checked_buttons_length){checked_buttons_length.preventDefault(),checked_buttons_length.stopPropagation(),this.checked?$(this).closest(".mcb-button").addClass("mcb-checked"):$(this).closest(".mcb-button").removeClass("mcb-checked");checked_buttons_length=option.builder.find(".mcb-checked").length;0===checked_buttons_length?$("#mcb-badge-length").removeClass().addClass("mcb-badge-disabled").text(0):$("#mcb-badge-length").removeClass().addClass("mcb-badge-enabled").text(checked_buttons_length)}),$("#mcb-bar-device").on("change","input",function(){"mcb-bar-device--none"===$(this).attr("id")?$("#mcb-badge-display").removeClass().addClass("mcb-badge-disabled").text(mcb.l10n.disabled):$("#mcb-badge-display").removeClass().addClass("mcb-badge-enabled").text(mcb.l10n.enabled)}),$(".mcb-settings").on("input change",".mcb-slider-input",function(){$(this).next("span").html(this.value+" "+$(this).data("postfix"))}),option.settings.on("keydown",function(event){27===event.which&&option.settings.find(".wp-picker-container").each(function(){$(this).hasClass("wp-picker-active")&&($(this).find(".color-picker").wpColorPicker("close"),$(this).find(".wp-color-result").focus())})}),$("#mcb-add-button").click(function(event){event.preventDefault(),event.stopPropagation();var buttonKey=option.builder.children(".mcb-button").maxKey("button")+1;$.ajax({url:ajaxurl,method:"POST",data:{action:"mcb_ajax_get_button",nonce:mcb.nonce,button_key:buttonKey},beforeSend:function(){$("#mcb-add-button").addClass("mcb-loading")},complete:function(){$("#mcb-add-button").removeClass("mcb-loading")}}).done(function(button){if(!button)return!1;var data=JSON.parse(button);if(!data.hasOwnProperty("summary")||!data.hasOwnProperty("details"))return!1;button=document.createElement("div");$(button).addClass(["mcb-button","mcb-opened"]).attr("data-button-key",buttonKey),$(button).append($(data.summary)).append($(data.details)),$(button).find(".color-picker").wpColorPicker(),$(button).find(".mcb-action-toggle-details").attr("aria-expanded","true"),option.builder.append(button)}).always(function(){$("#mcb-add-button").removeClass("mcb-loading")})}),option.builder.on("click",".mcb-action-delete-button",function(checked_buttons_length){checked_buttons_length.preventDefault(),checked_buttons_length.stopPropagation(),$(this).closest(".mcb-button").remove();checked_buttons_length=option.builder.find(".mcb-checked").length;0===checked_buttons_length?$("#mcb-badge-length").removeClass().addClass("mcb-badge-disabled").text(0):$("#mcb-badge-length").removeClass().addClass("mcb-badge-enabled").text(checked_buttons_length)}),option.builder.on("click",".mcb-action-toggle-details",function(event){event.preventDefault(),event.stopPropagation(),$(this).toggleAriaExpanded().closest(".mcb-button").toggleClass("mcb-opened")}),option.builder.on("click",".mcb-action-close-details",function(event){event.preventDefault(),event.stopPropagation(),$(this).closest(".mcb-button").removeClass("mcb-opened").find(".mcb-action-toggle-details").attr("aria-expanded","false"),document.getElementById("mcb-meta-box-builder").scrollIntoView()}),option.builder.on("click",".mcb-action-order-higher",function(button){button.preventDefault(),button.stopPropagation();var focused=document.activeElement,prev=$(this).closest(".mcb-button").prev(),button=$(this).closest(".mcb-button").detach();prev.before(button),focused.focus()}),option.builder.on("click",".mcb-action-order-lower",function(button){button.preventDefault(),button.stopPropagation();var focused=document.activeElement,next=$(this).closest(".mcb-button").next(),button=$(this).closest(".mcb-button").detach();next.after(button),focused.focus()}),option.builder.on("change",".mcb-details-type select",function(buttonKey){buttonKey.preventDefault(),buttonKey.stopPropagation();var input=$(this),button=$(this).closest(".mcb-button"),buttonKey=button.attr("data-button-key");$.ajax({url:ajaxurl,method:"POST",data:{action:"mcb_ajax_get_button_field",nonce:mcb.nonce,button_key:buttonKey,button_type:$(this).val()},beforeSend:function(){input.addClass("mcb-loading")},complete:function(){input.removeClass("mcb-loading")}}).done(function(data){if(!data)return!1;data=JSON.parse(data);if(!data.hasOwnProperty("button_field")||!data.hasOwnProperty("uri")||!data.hasOwnProperty("query"))return!1;["scrolltotop"].includes(data.button_field.type)?button.find(".mcb-summary-uri").text("#"):button.find(".mcb-summary-uri").text(data.button_field.uri||mcb.l10n.no_URI),button.find(".mcb-details-text input").val(data.button_field.text),button.find(".mcb-details-uri").replaceWith($(data.uri)),button.find(".mcb-builtin-query, .mcb-link-query, .mcb-builtin-parameter, .mcb-link-parameter").detach(),button.find(".mcb-details-uri").after($(data.query)),button.find(".mcb-details-type .mcb-description").text(data.button_field.desc_type)}).always(function(){input.removeClass("mcb-loading")})}),option.builder.on("click",".mcb-action-pick-icon",function(event){event.preventDefault(),event.stopPropagation(),setTimeout(function(){$("#mcb-icon-picker-container div input").focus()},100);var iconList,searchTerm,ti_path=mcb.plugin_url+"assets/svg/ti/tabler-sprite.svg",fa_path=mcb.plugin_url+"assets/svg/fa/sprites/",ti_filtered_icons=[],fa_filtered_icons=[],clickedButton=$(this),offset=clickedButton.offset(),button=$(this).closest(".mcb-button");$($.parseHTML($("#mcb-tmpl-icon-picker").html().replace(/\s{2,}/g,""))).css({top:offset.top-15,left:offset.left-(isRtl?185:0)}).appendTo("body").show(),iconList=$("#mcb-icon-picker-container ul"),fa_filtered_icons=filtered_icons(option.fa_icons,""),ti_filtered_icons=filtered_icons(option.ti_icons,""),$("body").off("click","#mcb-icon-picker-container button").on("click","#mcb-icon-picker-container button",function(brand){brand.preventDefault(),brand.stopPropagation();brand=$(this).attr("data-brand");$("#mcb-icon-picker-container").find("button").removeClass("mcb-brand-active"),$(this).addClass("mcb-brand-active"),"ti"===brand?ti_update_picker_window(iconList,ti_path,ti_filtered_icons,0):("fa"===brand||($("#mcb-icon-picker-container").find("button").removeClass("mcb-brand-active"),$("#mcb-icon-picker-container").find('button[data-brand="fa"]').addClass("mcb-brand-active")),fa_update_picker_window(iconList,fa_path,fa_filtered_icons,0))}),$("body").off("click","#mcb-icon-picker-container ul li a").on("click","#mcb-icon-picker-container ul li a",function(icon){icon.preventDefault(),icon.stopPropagation();var brand=$("#mcb-icon-picker-container").find("button.mcb-brand-active").attr("data-brand"),icon=$(this).closest("li").attr("data-icon"),names="fa"===brand?icon.split(" "):["",icon];if($("#mcb-icon-picker-container").remove(),!["ti","fa"].includes(brand)||"ti"===brand&&!option.ti_icons.includes(icon)||"fa"===brand&&!option.fa_icons.includes(icon))return button.blankIcon(),!1;$.ajax({url:ajaxurl,method:"POST",data:{action:"mcb_ajax_get_icon",nonce:mcb.nonce,brand:brand,group:names[0],icon:names[1]},beforeSend:function(){clickedButton.addClass("mcb-loading"),button.loadingIcon()},complete:function(){clickedButton.removeClass("mcb-loading"),button.find(".mcb-summary-icon").removeClass("mcb-loading-icon").end().find(".mcb-details-icon").removeClass("mcb-loading-icon")}}).done(function(svg){if(!svg)return button.blankIcon(),!1;svg=JSON.parse(svg);if(svg.length<=0)return button.blankIcon(),!1;button.find('input[name$="[brand]"]').val(brand).end().find('input[name$="[group]"]').val(names[0]).end().find('input[name$="[icon]"]').val(names[1]).end().find(".mcb-summary-brand").removeClass("mcb-blank-icon").text(brand.toUpperCase()).end().find(".mcb-summary-icon").removeClass(["mcb-blank-icon","mcb-fa"]).empty().append(svg).end().find(".mcb-details-brand").removeClass("mcb-blank-icon").text(brand.toUpperCase()).end().find(".mcb-details-icon").removeClass(["mcb-blank-icon","mcb-fa"]).empty().append(svg),"fa"===brand&&button.find(".mcb-summary-icon").addClass("mcb-fa").end().find(".mcb-details-icon").addClass("mcb-fa")}).always(function(){clickedButton.removeClass("mcb-loading"),button.find(".mcb-summary-icon").removeClass("mcb-loading-icon").end().find(".mcb-details-icon").removeClass("mcb-loading-icon")})}),$("body").off("click","#mcb-icon-picker-container div a").on("click","#mcb-icon-picker-container div a",function(brand){brand.preventDefault(),brand.stopPropagation();brand=$("#mcb-icon-picker-container").find("button.mcb-brand-active").attr("data-brand");"ti"===brand?("back"===$(this).attr("data-direction")?circular_window_backward:circular_window_forward)(iconList,ti_path,ti_filtered_icons,ti_update_picker_window):"fa"===brand?("back"===$(this).attr("data-direction")?circular_window_backward:circular_window_forward)(iconList,fa_path,fa_filtered_icons,fa_update_picker_window):($("#mcb-icon-picker-container").find("button").removeClass("mcb-brand-active"),$("#mcb-icon-picker-container").find('button[data-brand="fa"]').addClass("mcb-brand-active"),fa_update_picker_window(iconList,fa_path,fa_filtered_icons,0))}),$("body").off("input","#mcb-icon-picker-container div input").on("input","#mcb-icon-picker-container div input",function(brand){brand.preventDefault(),brand.stopPropagation();brand=$("#mcb-icon-picker-container").find("button.mcb-brand-active").attr("data-brand");searchTerm=$(this).val(),ti_filtered_icons=filtered_icons(option.ti_icons,searchTerm),fa_filtered_icons=filtered_icons(option.fa_icons,searchTerm),"ti"===brand?ti_update_picker_window(iconList,ti_path,ti_filtered_icons,0):("fa"===brand||($("#mcb-icon-picker-container").find("button").removeClass("mcb-brand-active"),$("#mcb-icon-picker-container").find('button[data-brand="fa"]').addClass("mcb-brand-active")),fa_update_picker_window(iconList,fa_path,fa_filtered_icons,0))}),$(document).off("mouseup").on("mouseup",function(event){event.preventDefault(),event.stopPropagation(),$("#mcb-icon-picker-container").is(event.target)||0!==$("#mcb-icon-picker-container").has(event.target).length||$("#mcb-icon-picker-container").remove()}),$(window).resize(function(){$("#mcb-icon-picker-container").is(event.target)||0!==$("#mcb-icon-picker-container").has(event.target).length||$("#mcb-icon-picker-container").remove()})}),option.builder.on("click",".mcb-action-clear-icon",function(event){event.preventDefault(),event.stopPropagation(),$(this).closest(".mcb-button").blankIcon()}),option.builder.on("input",".mcb-details-label input",function(event){event.preventDefault(),event.stopPropagation(),$(this).closest(".mcb-button").find(".mcb-summary-label").text($(this).val())}),option.builder.on("input",".mcb-details-uri input",function(button){button.preventDefault(),button.stopPropagation();var uri=$(this).val(),button=$(this).closest(".mcb-button");""===uri?button.find(".mcb-summary-uri").removeClass("mcb-monospace").text(mcb.l10n.no_URI):button.find(".mcb-summary-uri").addClass("mcb-monospace").text(uri)}),option.builder.on("click",".mcb-action-add-parameter",function(buttonKey){buttonKey.preventDefault(),buttonKey.stopPropagation();var input=$(this),button=$(this).closest(".mcb-button"),parameterKey=button.find(".mcb-link-parameter"),buttonKey=button.attr("data-button-key"),parameterKey=parameterKey.maxKey("parameter")+1;$.ajax({url:ajaxurl,method:"POST",data:{action:"mcb_ajax_get_parameter",nonce:mcb.nonce,button_key:buttonKey,parameter_key:parameterKey},beforeSend:function(){input.addClass("mcb-loading")},complete:function(){input.removeClass("mcb-loading")}}).done(function(parameter){if(!parameter)return!1;parameter=JSON.parse(parameter);if(parameter.length<=0)return!1;button.find(".mcb-link-query").after($(parameter))}).always(function(){input.removeClass("mcb-loading")})}),option.builder.on("click",".mcb-action-delete-parameter",function(event){event.preventDefault(),event.stopPropagation(),$(this).closest(".mcb-link-parameter").remove()})}};$(document).ready(option.init)}(jQuery,window,document);!function($){var backgroundImage,wpColorPickerAlpha={version:300};if("wpColorPickerAlpha"in window&&"version"in window.wpColorPickerAlpha){var version=parseInt(window.wpColorPickerAlpha.version,10);if(!isNaN(version)&&wpColorPickerAlpha.version<=version)return}Color.fn.hasOwnProperty("to_s")||(Color.fn.to_s=function(type){var color="";return"hex"===(type="hex"===(type=type||"hex")&&this._alpha<1?"rgba":type)?color=this.toString():this.error||(color=this.toCSS(type).replace(/\(\s+/,"(").replace(/\s+\)/,")")),color},window.wpColorPickerAlpha=wpColorPickerAlpha,$.widget("a8c.iris",$.a8c.iris,{alphaOptions:{alphaEnabled:!(backgroundImage="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAIAAAHnlligAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAHJJREFUeNpi+P///4EDBxiAGMgCCCAGFB5AADGCRBgYDh48CCRZIJS9vT2QBAggFBkmBiSAogxFBiCAoHogAKIKAlBUYTELAiAmEtABEECk20G6BOmuIl0CIMBQ/IEMkO0myiSSraaaBhZcbkUOs0HuBwDplz5uFJ3Z4gAAAABJRU5ErkJggg==")},_getColor:function(color){return void 0===color&&(color=this._color),this.alphaOptions.alphaEnabled?(color=color.to_s(this.alphaOptions.alphaColorType),this.alphaOptions.alphaColorWithSpace?color:color.replace(/\s+/g,"")):color.toString()},_create:function(){try{this.alphaOptions=this.element.wpColorPicker("instance").alphaOptions}catch(e){}$.extend({},this.alphaOptions,{alphaEnabled:!1,alphaCustomWidth:130,alphaReset:!1,alphaColorType:"hex",alphaColorWithSpace:!1}),this._super()},_addInputListeners:function(input){function callback(event){var val=input.val(),color=new Color(val),val=val.replace(/^(#|(rgb|hsl)a?)/,""),type=self.alphaOptions.alphaColorType;input.removeClass("iris-error"),color.error?""!==val&&input.addClass("iris-error"):"hex"===type&&"keyup"===event.type&&val.match(/^[0-9a-fA-F]{3}$/)||color.toIEOctoHex()!==self._color.toIEOctoHex()&&self._setOption("color",self._getColor(color))}var self=this;input.on("change",callback).on("keyup",self._debounce(callback,100)),self.options.hide&&input.one("focus",function(){self.show()})},_initControls:function(){var self,stripAlpha,stripAlphaSlider,controls;this._super(),this.alphaOptions.alphaEnabled&&(stripAlphaSlider=(stripAlpha=(self=this).controls.strip.clone(!1,!1)).find(".iris-slider-offset"),controls={stripAlpha:stripAlpha,stripAlphaSlider:stripAlphaSlider},stripAlpha.addClass("iris-strip-alpha"),stripAlphaSlider.addClass("iris-slider-offset-alpha"),stripAlpha.appendTo(self.picker.find(".iris-picker-inner")),$.each(controls,function(k,v){self.controls[k]=v}),self.controls.stripAlphaSlider.slider({orientation:"vertical",min:0,max:100,step:1,value:parseInt(100*self._color._alpha),slide:function(event,ui){self.active="strip",self._color._alpha=parseFloat(ui.value/100),self._change.apply(self,arguments)}}))},_dimensions:function(strip){if(this._super(strip),this.alphaOptions.alphaEnabled){var opts=this.options,square=this.controls.square,strip=this.picker.find(".iris-strip"),innerWidth=Math.round(this.picker.outerWidth(!0)-(opts.border?22:0)),squareWidth=Math.round(square.outerWidth()),stripWidth=Math.round((innerWidth-squareWidth)/2),stripMargin=Math.round(stripWidth/2),totalWidth=Math.round(squareWidth+2*stripWidth+2*stripMargin);while(innerWidth<totalWidth)stripWidth=Math.round(stripWidth-2),stripMargin=Math.round(stripMargin-1),totalWidth=Math.round(squareWidth+2*stripWidth+2*stripMargin);square.css("margin","0"),strip.width(stripWidth).css("margin-left",stripMargin+"px")}},_change:function(){var controls,alpha,gradient,self=this,active=self.active;self._super(),self.alphaOptions.alphaEnabled&&(controls=self.controls,alpha=parseInt(100*self._color._alpha),gradient=["rgb("+(gradient=self._color.toRgb()).r+","+gradient.g+","+gradient.b+") 0%","rgba("+gradient.r+","+gradient.g+","+gradient.b+", 0) 100%"],self.picker.closest(".wp-picker-container").find(".wp-color-result"),self.options.color=self._getColor(),controls.stripAlpha.css({background:"linear-gradient(to bottom, "+gradient.join(", ")+"), url("+backgroundImage+")"}),active&&controls.stripAlphaSlider.slider("value",alpha),self._color.error||self.element.removeClass("iris-error").val(self.options.color),self.picker.find(".iris-palette-container").on("click.palette",".iris-palette",function(){var color=$(this).data("color");self.alphaOptions.alphaReset&&(self._color._alpha=1,color=self._getColor()),self._setOption("color",color)}))},_paintDimension:function(origin,control){var color=!1;this.alphaOptions.alphaEnabled&&"strip"===control&&(color=this._color,this._color=new Color(color.toString()),this.hue=this._color.h()),this._super(origin,control),color&&(this._color=color)},_setOption:function(key,value){if("color"!==key||!this.alphaOptions.alphaEnabled)return this._super(key,value);value=""+value,newColor=new Color(value).setHSpace(this.options.mode),newColor.error||this._getColor(newColor)===this._getColor()||(this._color=newColor,this.options.color=this._getColor(),this.active="external",this._change())},color:function(newColor){return!0===newColor?this._color.clone():void 0===newColor?this._getColor():void this.option("color",newColor)}}),$.widget("wp.wpColorPicker",$.wp.wpColorPicker,{alphaOptions:{alphaEnabled:!1},_getAlphaOptions:function(){var el=this.element,type=el.data("type")||this.options.type,color=el.data("defaultColor")||el.val(),options={alphaEnabled:el.data("alphaEnabled")||!1,alphaCustomWidth:130,alphaReset:!1,alphaColorType:"rgb",alphaColorWithSpace:!1};return options.alphaEnabled&&(options.alphaEnabled=el.is("input")&&"full"===type),options.alphaEnabled&&(options.alphaColorWithSpace=color&&color.match(/\s/),$.each(options,function(name,defaultValue){var value=el.data(name)||defaultValue;switch(name){case"alphaCustomWidth":value=value?parseInt(value,10):0,value=isNaN(value)?defaultValue:value;break;case"alphaColorType":value.match(/^(hex|(rgb|hsl)a?)$/)||(value=color&&color.match(/^#/)?"hex":color&&color.match(/^hsla?/)?"hsl":defaultValue);break;default:value=!!value}options[name]=value})),options},_create:function(){$.support.iris&&(this.alphaOptions=this._getAlphaOptions(),this._super())},_addListeners:function(){if(!this.alphaOptions.alphaEnabled)return this._super();var self=this,el=self.element,isDeprecated=self.toggler.is("a"),id=self.element.attr("id");self.element.attr("id",null),self.toggler.attr("id",id),this.alphaOptions.defaultWidth=el.width(),this.alphaOptions.alphaCustomWidth&&el.width(parseInt(this.alphaOptions.defaultWidth+this.alphaOptions.alphaCustomWidth,10)),self.toggler.css({position:"relative","background-image":"url("+backgroundImage+")"}),isDeprecated?self.toggler.html('<span class="color-alpha" />'):self.toggler.append('<span class="color-alpha" />'),self.colorAlpha=self.toggler.find("span.color-alpha").css({width:"30px",height:"100%",position:"absolute",top:0,"background-color":el.val()}),isRtl?self.colorAlpha.css({"border-bottom-right-radius":"2px","border-top-right-radius":"2px",right:0}):self.colorAlpha.css({"border-bottom-left-radius":"2px","border-top-left-radius":"2px",left:0}),el.iris({change:function(event,ui){self.colorAlpha.css({"background-color":ui.color.to_s(self.alphaOptions.alphaColorType)}),$.isFunction(self.options.change)&&self.options.change.call(this,event,ui)}}),self.wrap.on("click.wpcolorpicker",function(event){event.stopPropagation()}),self.toggler.on("click",function(){self.toggler.hasClass("wp-picker-open")?self.close():self.open()}),el.change(function(event){var val=$(this).val();(el.hasClass("iris-error")||""===val||val.match(/^(#|(rgb|hsl)a?)$/))&&(isDeprecated&&self.toggler.removeAttr("style"),self.colorAlpha.css("background-color",""),$.isFunction(self.options.clear)&&self.options.clear.call(this,event))}),self.button.on("click",function(event){$(this).hasClass("wp-picker-default")?el.val(self.options.defaultColor).change():$(this).hasClass("wp-picker-clear")&&(el.val(""),isDeprecated&&self.toggler.removeAttr("style"),self.colorAlpha.css("background-color",""),$.isFunction(self.options.clear)&&self.options.clear.call(this,event),el.trigger("change"))})}}))}(jQuery),jQuery(function(){jQuery(".color-picker").wpColorPicker()});