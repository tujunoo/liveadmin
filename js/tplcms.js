/**
 * @author Ridge
 * @author Alisa
 */

/* global jQuery, toMarkdown, define, pageData, insertData, Showdown, juicer  */

"use strict";
(function(f) {
	if (typeof define === "function") {
		define(f);
	} else {
		f();
	}
})(function(require) {
	jQuery(function($) {
		var resgiterJsCss = "{{ void(App.clientScript.registerCssFile('/css/base.css')) }} \n {{ void(App.clientScript.registerCssFile('/style/base.css')) }} \n {{ void(App.clientScript.registerCssFile('/style/pagetpl.css')) }} \n {{ void(App.clientScript.registerSeaJsFile(['prefixfree','pagetpl'])) }}\n{{ void(App.clientScript.registerMetaTag('width=1400', 'viewport')) }}",
			prefix = document.documentMode ? "ms" : (window.opera ? "o" : (window.netscape ? "moz" : "webkit")), //后缀
			dialog_modify = $(".dialog_modify").submit(function(e) {
				e.preventDefault();
				if (this.checkValidity()) {

				}
			}).tabs().dialog({
				autoOpen: false,
				minWidth: 640
			}), //表单
			contextMenu = $("<ul>").addClass("dev_contextmenu").menu().appendTo("body"), //右键菜单
			converter = new Showdown.converter(), //markdown转换
			saveStatus = false, //是否点击保存按钮
			dataCache = {}, //data存储
			filesCache = {}, //文件存储
			uniqueID = 0, //唯一id标识
			modActivity, //当前活动对象
			rootMod, //根模块
			lang; //当前语言



		//向页面上的图片选择框添加选项
		function append(key, url) {
				url = url || key;
				$(".ui_file select").append(function() {
					return new Option(key, $(this).is("[name^='background']") ? "url(" + url + ")" : url);
				});
			}
			//设置图片url
		function addfile() {
				/*jshint validthis:true */
				$.each(this.files, function(i, file) {
					var url = (window.URL || window.webkitURL).createObjectURL(file);
					if (!filesCache[url]) {
						filesCache[url] = file;
						append(file.name, url);
					}
				});
			}
			//递归循环数据，append url into page's select
		function loopData(data) {
				if ($.isArray(data) || $.isPlainObject(data)) {
					$.each(data, function(i, data) {
						loopData(data);
					});
				} else if (/((\w+:)?\/\/[^\)]+\.(jpe?g|gif|png))/.test(data)) {
					var url = RegExp.$1;
					if (!filesCache[url]) {
						append(url.replace(/^.+\//, ""), url);
					}
				}
			}
			//init url
		loopData(pageData);

		//生成唯一的、不重复的ID
		function newID(str) {
			var id = str + uniqueID++;
			if (id in dataCache) {
				id = newID(str);
			}
			return id;
		}

		//Module对象构造函数
		function Module(data) {
				this.type = data.type;
				//set module classname
				this.className(data.type);
				//set module id
				this.id(data.id || newID("module_" + data.type + "_"));
				//set module href
				this.href(data.href);
				//update view
				this.update(data);
			}
			//模块包裹标签
		Module.prototype.tagName = "div";


		/**
		 * 设置或获取模块的id
		 * @param  {[string]} id id字符串
		 * @return {[type]}    [description]
		 */
		Module.prototype.id = function(id) {
			if (id) {
				//如果dataCache存在id,则alert提示
				if (this._id) {
					try {
						delete dataCache[this._id];
					} catch (ex) {
						dataCache[this._id] = false;
					}
				}
				this._id = id;

				//给_node添加id
				if (this._node) {
					this._node.prop("id", id);
				}
				//给模块头部span添加文本
				if (this._warp) {
					$(this._warp).find(".dev_module_header>span").text(id);
				}


				//存储当前module
				dataCache[id] = this;
				return this._id;
			} else {
				return this._id;
			}
		};

		/**
		 * 查找a标签，替换href
		 * @param  {String} href href字符串
		 */
		Module.prototype.href = function(href) {
			if (href) {
				this._href = href;
				if (this._node) {
					var tagname = $(this._node).get(0).tagName,
						node = (tagname === "A") ? $(this._node) : ($(this._node).find("a").length === 0 ? "" : $(this._node).find("a"));
					if (node !== "") {
						node.attr("href", href);
					}
				}
				return this._href;
			} else {
				return this._href;
			}
		};


		/**
		 * 设置或者获取模块的class
		 * @param  {[String]} name class字符串
		 * @return {[String]}      class字符串
		 */
		Module.prototype.className = function(name) {
			try {
				this._className = $.trim(name.toString());
				if (this._node) {
					this._node.prop("class", this._className);
					this._node.addClass("dev_module");
				}
			} catch (ex) {
				return $.trim("dev_module " + (this._className || ""));
			}
		};


		/**
		 * 获取或者设置模块的节点
		 * @param  {[object]} node dom节点
		 * @return {[object]}      dom节点
		 */
		Module.prototype.node = function(node) {
			//如果有node参数，则把this._node换成node
			if (node) {
				if (this._node) {
					this._node.replaceWith(node);
				}
				this._node = node;
				//否则返回this._node
			} else {
				return this._node || (this._node = $("<" + this.tagName + ">", {
					"class": this.className(),
					id: this.id()
				}));
			}
		};


		/**
		 * 为模块包裹编辑辅助用元素
		 * @return {[object]} 返回包裹有编辑元素的dom节点
		 */
		Module.prototype.getWrap = function() {
			if (!this._warp) {
				this._warp = $("<div class=\"dev_module_helper\"><div class=\"dev_module_header\"> <div class=\"btns\"> <span class=\"modify ui-icon ui-icon-gear\"></span> <span class=\"del ui-icon ui-icon-trash\"></a> </div> <span>" + this.id() + "</span> </div></div>").append(this.node());
				this._warp.find(".dev_module_header .btns>*").button();
			}
			return this._warp;
		};

		/**
		 * 模块视图更新、样式更新、图片更新
		 * @param  {[json]} data json数据
		 * @return {[type]}      无
		 */
		Module.prototype.update = function(data) {
			data = data || this.data;
			this.className(data.className);

			var tpl = $("#" + this.type).html();
			switch (this.type) {
				case "img_list":
				case "produce":
				case "map":
				case "simulate_area":
					this.node($(juicer(tpl, data)).prop("id", this.id()).prop("class", this.className()));
					break;
				case "img_nav":
					data.map = data.map || newID("module_map_");
					this.node().html(juicer(tpl, data));
					break;
				case "scroll_nav":
				case "img_nav":
				case "banner":
				case "bshare":
				case "image":
					this.node().html(juicer(tpl, data));
					break;
				case "text":
					this.node().html(converter.makeHtml(data.markdown));
					break;
				default:
					this.node();
			}

			//切换图片的简繁体
			if (data.src) {
				var node = (this.node().get(0).tagName === "IMG") ? this.node() :
					($(this._node).find("img").length === 0 ? "" : $(this._node).find("img"));

				if (node && !saveStatus) {
					node.prop("src", (lang === "tw") ? (data.src_tw || data.src) : data.src);
					node.attr("src_sc", data.src);
					node.attr("src_tw", data.src_tw);
				} else {
					node.prop("src", "{{ lang == 'tw' ? '" + (data.src_tw || data.src) + "' : '" + data.src + "'}}");
				}
			}
			//如果data.css存在，则把data.css用extend添加到对象的css，并把css相关属性添加到节点的style属性中，背景图片简繁切换
			if (data.css) {
				var bgImg = data.css["background-image-" + lang] || data.css["background-image"],
					dataCss = $.extend({}, data.css),
					style = "";

				if (bgImg) {
					if (saveStatus) {
						bgImg = "{{ lang=='tw' ? '" + (data.css["background-image-tw"] || data.css["background-image"]) + "' : '" + data.css["background-image"] + "'}}";
					}

					$.extend(dataCss, {
						//简繁体图片
						"background-image": bgImg
					});
				}

				$.each(dataCss, function(name, value) {
					if (value) {
						if (name === "column-count") {
							style += "-webkit-" + name + ":" + value + ";";
						}
						style += name + ":" + value + ";";
					}
				});

				//引用prefixfree模块
				if (!saveStatus) {
					style = require("prefixfree").prefixCSS(style, false);
				}

				this.node().attr("style", style);
			}
			this.data = data;
		};

		/**
		 * 递归循环数据,获取包裹有编辑元素的节点
		 * @param  {[json]} data json数据
		 * @return {[object]}    dom对象
		 */
		function getNode(data) {
			//实例化Module
			var obj = new Module(data);
			//递归返回module对象
			if (data.children && data.children.length) {
				$(obj.node()).append($.map(data.children, function(data) {
					if (data.type === "simulate_area") {
						return getNode(data).node();
					} else {
						return getNode(data).getWrap();
					}
				}));
			}
			return obj;
		}


		//根据序列化数据来获得html
		function getHTML(data) {
			saveStatus = true;
			var obj = new Module(data);

			if (data.children && data.children.length) {
				var node = obj.node().removeClass("dev_module");

				node.append($.map(data.children, function(data) {
					return getHTML(data).node().removeClass("dev_module");
				}));
			}
			return obj;
		}


		/**
		 * 数据序列化module
		 * @param {[object]} node dom对象
		 */
		function DataModule(node) {
			this._node = node;
			this.dataObj = {};
			this.id();
			this.css();
			this.type();
			this.src();
			this.href();
			this.markdown();
		}

		/**
		 * 获取相应方法对应的属性节点(标签名或者class属性)
		 * @param  {[object]} node dom对象
		 * @param  {[string]} tag  元素标签
		 * @param  {[string]} str  class字符串
		 * @return {[object]}      dom对象
		 */
		function getMethodNode(node, tag, str) {
			var methodNode, eles;
			if (node.hasClass(str) || node[0].tagName === tag) {
				methodNode = node;
			} else {
				//当前模块为独立的模块
				if (node.find(".dev_module_helper").length === 0) {
					eles = tag ? node.find(tag.toLowerCase()) : node.find("." + str);
					if (eles && eles.length) {
						methodNode = eles;
					}
				}
			}
			return methodNode;
		}


		DataModule.prototype.src = function() {
			//先获取节点
			var node = getMethodNode(this._node, "IMG"),
				src_tw, src;

			if (node) {
				src_tw = node.attr("src_tw");
				src = node.attr("src_sc");

				this.dataObj.src = src;
				if (src_tw) {
					this.dataObj.src_tw = src_tw || src;
				}
			}
		};
		DataModule.prototype.href = function() {
			var node = getMethodNode(this._node, "A");
			if (node) {
				this.dataObj.href = node.attr("href");
			}
		};
		DataModule.prototype.markdown = function() {
			var text = getMethodNode(this._node, "", "text");
			if (text) {
				this.dataObj.markdown = toMarkdown(text.html());
			}
		};
		DataModule.prototype.id = function() {
			if (this._node) {
				this.dataObj.id = this._node.attr("id");
			}
		};
		DataModule.prototype.css = function() {
			var css = {};
			if (this._node) {
				var styles = this._node.attr("style");
				if (styles) {
					styles = styles.split(";");
					for (var i = 0; i < styles.length; i++) {
						var name = styles[i].substring(0, styles[i].indexOf(":")),
							val = styles[i].substring(styles[i].indexOf(":") + 1, styles[i].length);
						if (name) {
							css[name] = val;
						}
					}
				}
			}
			this.dataObj.css = css;
		};
		DataModule.prototype.type = function() {
			var classes = this._node.attr("class").split(" "),
				matchArr = ["dev_module", "ui-sortable", "lang_sc", "lang_tw"];
			//排除法
			for (var i = 0; i < classes.length; i++) {
				if ($.inArray(classes[i], matchArr) === -1) {
					this.dataObj.type = classes[i];
					if (this.dataObj.type === "img_nav") {
						this.dataObj.map = this._node.find("map").prop("id");
					}
				}
			}
		};
		//循环node，让相关数据序列化
		function getData(node) {
			var obj = new DataModule(node),
				children = node.find(" > .dev_module_helper");

			if (children && children.length) {
				obj.dataObj.children = obj.dataObj.children || [];

				$.each(children, function() {
					var dev_module = $(this).find(" > .dev_module");
					obj.dataObj.children.push(getData(dev_module).dataObj);
				});
			}
			return obj;
		}


		//生成根模块
		pageData.id = pageData.type = "pagetpl";
		//将根模块插入到页面
		rootMod = getNode(pageData).node().insertBefore(".dev_bar");
		//头部编辑菜单初始化
		$(".dev_bar>div>ul").menu();
		$(".dev_bar>div>span").button({
			icons: {
				secondary: "ui-icon-triangle-1-s"
			}
		});

		// 将css私有属性前缀作为class添加给html标签，方便给css写hack
		$("html").addClass(prefix);
		//给分栏表单元素添加prefix
		// $("input[name='column-count']").prop("name", "-" + prefix + "-column-count");

		//设计当前处于可编辑状态的模块
		function setActivity(elem) {
			elem = $(elem);

			//为活动模块添加类
			$(".dev_activity").not($(elem).addClass("dev_activity")).removeClass("dev_activity");
			//获取活动模块
			modActivity = dataCache[elem.children(".dev_module").prop("id") || rootMod.prop("id")];

			//查找父级模块
			elem = elem.parent(".dev_module");
			//如果没有找到父级模块，则查找根模块
			if (!elem.length) {
				elem = rootMod;
			}

			//关闭其他项目的排序功能
			$(".ui-sortable").not(elem).sortable("destroy");

			//为当前可编辑模块设置排序功能
			elem.not(".scrollfix").sortable({
				placeholder: "ui-sortable-placeholder",
				handle: ".dev_module_header"
			});
		}

		//初始化modActivity
		setActivity();

		//文本框按光标位置插入字符串
		function insertText(obj, str) {
			//IE
			if (document.selection) {
				var sel = document.selection.createRange();
				sel.text = str;
			} else if (typeof obj.selectionStart === "number" && typeof obj.selectionEnd === "number") {
				var startPos = obj.selectionStart,
					endPos = obj.selectionEnd,
					cursorPos = startPos,
					tmpStr = obj.value;
				//把str拼接到obj.value中
				obj.value = tmpStr.substring(0, startPos) + str + tmpStr.substring(endPos, tmpStr.length);
				cursorPos += str.length;
				obj.selectionStart = obj.selectionEnd = cursorPos;
			} else {
				obj.value += str;
			}
			obj.focus();
		}

		//组件插入
		function insertComponent(data, type) {
			if (data.children && data.children.length) {
				$.map(data.children, function(data) {
					//递归循环数据，找到相应type的数据
					if (data.children) {
						insertComponent(data, type);
					}
					//如果找到了相应type的数据,则执行相应操作
					if (data.type === type) {
						var insertDataCache = [];
						insertDataCache.push(data);

						//判断当前是否可插入
						if (typeJudge(type)) {
							//创建type实例
							var module = getNode(insertDataCache[0]).getWrap(),
								root = typeJudge(type);
							//将实例设置为当前活动模块
							setActivity(module);
							//更新视图
							root.append(module);
						}
					}
				});
			}
		}

		//组件插入判断
		function typeJudge(type) {
			var root = rootMod;
			//判断当前模块是否可以插入，和插入到哪里？
			switch (type) {
				case "produce":
					if (modActivity.type === "product_list") {
						root = modActivity._node;
					} else {
						root = false;
						window.alert("当前模块不可以插入产品，请先选择产品列表为当前活动模块，或者插入产品列表组件");
					}
					break;
				case "img_list":
					if (modActivity.type === "pic_list") {
						root = modActivity._node;
					} else {
						root = false;
						window.alert("当前模块不可以插入广告图，请先选择图片列表为当前活动模块，或者插入图片列表组件");
					}
					break;
				case "bshare":
					if (modActivity.type === "pagebtns") {
						root = modActivity._node;
					} else {
						root = false;
						window.alert("当前模块不可以插入分享按钮，请先选择分享模块为当前活动模块，或者插入分享模块组件");
					}
					break;
				case "scroll_nav":
					if (modActivity.type === "navbar") {
						root = modActivity._node;
					} else {
						root = false;
						window.alert("当前模块不可以插入导航菜单，请先选择滚动导航为当前活动模块，或者插入滚动导航组件");
					}
					break;
				case "navbar":
				case "pagebtns":
					if (modActivity.type === "scrollfix") {
						root = modActivity._node;
					} else {
						root = false;
						window.alert("当前模块不可以插入滚动导航，请先选择滚动组件为当前活动模块，或者插入滚动组件");
					}
					break;
				case "image":
					if (modActivity.type === "common_plate" || modActivity.type === "navbar") {
						root = modActivity._node;
					} else {
						root = rootMod;
					}
					break;
				default:
					if (modActivity && modActivity.type === "common_plate") {
						root = modActivity._node;
					} else {
						root = rootMod;
					}
			}
			return root;
		}


		//更新html
		function updateHTML(dom) {
			var simulateAreas = $(".simulate_area"),
				imgs = dom.find("img");

			//console.log(simulateAreas.length);
			//将模拟热点区域转化为map节点
			$.each(simulateAreas, function() {
				var area = $("<area shape='rect' href='" + $(this).attr("href") + "' coords='" + $(this).attr("data-coords") + "'></area>"),
					name = $(this).attr("data-usemap");
				dom.find("map[name='" + name + "']").append(area);
			});

			//img标签去掉src_sc和src_tw
			$.each(imgs, function() {
				$(this).removeAttr("src_sc").removeAttr("src_tw");
			});

			return dom;
		}


		//鼠标右键操作
		$(document).on("contextmenu", function(e) {
			//按下Ctrl和Shift键时除外
			if (!(e.shiftKey || e.ctrlKey)) {
				e.preventDefault();

				var modules = $(e.target).parents(".dev_module_helper");


				if (modules.length > 1) {
					contextMenu.empty().append(modules.map(function() {
						//生成菜单项，绑定点击事件
						var module = $(this);
						return $("<li>").text(module.children(".dev_module").attr("id") || "undefined").click(function() {

							setActivity(module);
						})[0];
					})).menu("refresh").show().css({
						left: e.pageX,
						top: e.pageY
					});
				} else {
					//只找到一个模块，则直接设置为当前项
					setActivity(modules);
				}
			}
		}).on("click", ".dev_module_header .del", function() {
			//弹出对话框询问是否删除
			if (window.confirm("确定要删除“" + $(this).closest(".dev_module_header").children("span").text() + "”吗?")) {
				$(this).closest(".dev_module_helper").remove();
			}
		}).on("click", ".dev_module_header .modify", function() {
			//弹出属性对话框
			dialog_modify.dialog("option", "title", modActivity.id() + "属性").dialog("open");

			var dialogMenu = $(".dialog_modify ul a"),
				dialogHtml = dialogMenu.filter("[href='#dialog_html']");

			//根据特定type显示特定tab
			switch (modActivity.type) {
				case "scroll_nav":
				case "produce":
				case "text":
					dialogHtml.show();
					break;
				default:
					//默认情况内容区域和图像映射区域隐藏
					dialogMenu.show();
					dialogHtml.hide();
			}
			//重置mark编辑文本框的内容
			dialog_modify.find(".markdown").val(modActivity.data.markdown || "");


			//重置与css相关的表单元素
			if (modActivity.data.css) {
				$.each(modActivity.data.css, function(name, value) {
					var input = dialog_modify.find("[name='" + name + "']"),
						valueFilter = "[value='" + value + "']";

					if (!(input.filter(valueFilter).prop("checked", true).length || input.find(valueFilter).prop("selected", true).length)) {
						input.val(value);
					}
				});
			}

			//重置各个与模块属性相关表单元素
			$.each(modActivity, function(prop, val) {
				 if (val !== undefined) {
					val = val.call ? val.call(modActivity) : val;
					dialog_modify.find("#" + prop).val(val);
				}
			});

			//重置颜色选择
			$(".ui_color :text").change();


			//重置tab的第一项
			dialog_modify.find(".ui-tabs-nav a:visible").first().click();
		});


		//鼠标拖拽操作
		(function() {
			var startX, startY, //鼠标点击开始坐标
				simulateMap, //模拟热点区域
				moveX, moveY, //鼠标移动坐标
				offset = {}, //usemap的相对偏移量
				endX, endY, //鼠标点击结束坐标
				status, //是否生成相应节点
				key; //鼠标点击开关

			/**
			 * 模拟热点
			 * @param {object} wrap 图片导航容器
			 * @param {obejct} obj  集合相应数据的对象
			 */
			function setArea(wrap, obj) {
				var btns = "<div class='btns'><span class=\"modify ui-icon ui-icon-gear\"></span> <span class=\"del ui-icon ui-icon-trash\"></span></div>",
					dialog = "<div class='dialog'><label>锚点: <input type='text' id='href'></label></div>",
					usemap = modActivity._node.find("map").attr("name"),
					simulate = $("<a href='#' data-coords='" + obj.coords.join(",") + "' class='simulate_area' data-usemap='"+ usemap +"'></a>").css({
						background: "rgba(0,0,0,0.2)",
						border: "1px solid gray",
						left: obj.pageX + "px",
						top: obj.pageY + "px",
						position: "absolute",
						height: obj.moveX,
						width: obj.moveY,
						display: "block",
					}).append(btns).append(dialog);

				//append node
				wrap.append(simulate);

				//modify area
				modifyArea(simulate);

				return simulate;
			}

			/**
			 * 调整area区域
			 * @param  {object} node 虚拟热点节点
			 */
			function modifyArea(node) {
				var dialog_form = node.find(".dialog").dialog({
					autoOpen: false,
					width: 300,
					height: 100
				});

				//set buttom element
				node.find(".btns>*").button();

				//可拖拽、可改变大小
				node.resizable().draggable();	

				//监听resize和drag事件
				node.on("resize drag", function() {
					if (modActivity.type === "img_nav") {
						var me = $(this),
							usemap = modActivity._node.find("[usemap]"),
							offset = usemap.offset(),
							startX = parseInt(me.css("left")) - offset.left, //相对于usemap的left
							startY = parseInt(me.css("top")) - offset.top, //相对于usemap的top
							endY = me.height() + startY,
							endX = me.width() + startX;

						//console.log(usemap);
						//update coords
						me.attr("data-coords", startX + "," + startY + "," + endX + "," + endY);
					}
				});


				//bind click
				node.on("click", function(e) {
					var target = e.target || e.srcElement,
						parent = $(target).closest(".simulate_area"),
						classStr = $(target).closest(".ui-icon").attr("class") || "";

					if (classStr.indexOf("del") !== -1) {
						e.preventDefault();

						//移除当前node
						parent.remove();
					}
					if (classStr.indexOf("modify") !== -1) {
						var href = parent.attr("href");

						//阻止默认的锚点动作
						e.preventDefault();
						//显示dialog
						dialog_form.dialog("option", "title", "area属性").dialog("open");

						//重置dialog的href文本框
						dialog_form.find("input[id='href']").val(href);

						//当href文本框的值发生改变时，重置node的href属性
						dialog_form.find("input[id='href']").change(function() {
							parent.attr("href", this.value);
						});
					}

				});
			}

			$(document).on("mousedown", ".img_nav", function(e) {
				//初始化
				offset = $(this).find("[usemap]").offset() || {};
				startX = e.pageX - offset.left;
				startY = e.pageY - offset.top;
				status = 1;
				key = 1;
			}).on("mouseup resize drag", function() {
				//重置开关
				status = 0;
				key = 0;
			}).on("mousemove", ".img_nav", function(e) {
				//记录移动位置
				endX = e.pageX;
				endY = e.pageY;
				moveX = e.pageX - offset.left - startX;
				moveY = e.pageY - offset.top - startY;

				//当在img_nav按下鼠标移动的时候,并且有活动模块为图片导航
				if (key && modActivity.type === "img_nav") {
					//console.log(modActivity);
					//当move的鼠标值超过10的时候
					if (moveX > 10 || moveY > 10) {
						var obj = {
								coords: [startX, startY, endX, endY],
								pageX: e.pageX,
								pageY: e.pageY,
								moveX: moveX,
								moveY: moveY
							};

						//判断是否应该生成node,无则调用函数生成,并把status重置为0
						if (status) {
							//获取模拟热点
							simulateMap = setArea(rootMod, obj);

							//重置status
							status = 0;
						}

						//动态调整宽高和coords属性
						simulateMap.css({
							width: moveX + "px",
							height: moveY + "px"
						}).attr("data-coords", obj.coords.join(","));
					}
				}
			});



			$(document).ready(function() {
				$(".simulate_area").each(function() {
					var me = $(this);
					modifyArea(me);
				});
			});


		})();



		//颜色选择器
		$(".ui_color").each(function() {
			var parent = $(this),
				text = parent.find(":text"),
				color = parent.find("[type='color']");

			color.on("change input", function(e) {
				text.val(this.value).trigger(e.type);
				return false;
			});

			text.on("change input", function() {
				var width = color.outerWidth(),
					pos1 = width + "px",
					pos2 = width + 1 + "px",
					pos3 = width + 4 + "px",
					colorval = this.value || "rgba(0,0,0,0)";

				colorval = colorval + ", " + colorval + " " + pos1 + ", #ccc " + pos1 + ", #ccc " + pos2 + ", rgba(0, 0, 0, 0.1) " + pos1 + ", rgba(0,0,0,0) " + pos3 + ", rgba(0,0,0,0))";

				$(this).css({
					// 搜私有前缀语法
					backgroundImage: "-" + prefix + "-linear-gradient(right," + colorval
				});
				$(this).css({
					// w3C
					backgroundImage: "linear-gradient(to left," + colorval
				});
			});
		});


		//对话框编辑
		dialog_modify.on("change input", ".markdown", function() {
			//编辑markdown文本
			if (modActivity.data.markdown !== undefined) {
				modActivity.data.markdown = $(this).val();
				modActivity.update();
			}
		}).on("change input autocompletechange", "[name]", function() {
			//编辑css
			modActivity.data.css = modActivity.data.css || {};
			modActivity.data.css[this.name] = this.disabled ? "" : $(this).val();
			modActivity.update();
		}).on("change", "#dialog_img .ui_file select", function() {
			//编辑图片
			var id = $(this).attr("id");
			//与简体一致
			if (id === "src_tw" && this.value === "") {
				modActivity.data.src_tw = modActivity.data.src;
			} else {
				modActivity.data[id] = this.value;
			}

			modActivity.update();
		}).on("change", "#dialog_html .ui_file select", function() {
			//编辑文本图片
			//获取选中状态的option
			var selected = this.options[this.selectedIndex];

			if (selected.value) {
				//生成markdown图片格式
				selected = "![" + $(selected).text() + "](" + selected.value + ")";

				$(".markdown").each(function() {
					insertText(this, selected);
				}).change();

				this.selectedIndex = 0;
			}

		}).on("change", "#dialog_bg .ui_file select", function() {
			//与简体背景图片一致
			if (this.name === "background-image-tw" && this.value === "") {
				modActivity.data.css["background-image-tw"] = modActivity.data.css["background-image"];
			} else {
				modActivity.data.css[this.name] = this.value;
			}

			modActivity.update();
		}).on("change", "[id='id']", function() {
			//属性对话框修改id

			//改变对话框的title文本
			if (dataCache[this.value]) {
				window.alert("命名冲突，此ID已被占用");
			} else {
				dialog_modify.dialog("option", "title", modActivity.id(this.value) + " 属性");

				modActivity.data.id = this.value;
				modActivity.update();
			}
		}).on("change", "[id='href']", function() {
			//编辑链接
			if (this.value) {
				modActivity.data.href = this.value;
				modActivity.href(this.value);
			}
			modActivity.update();
		}).on("change", function(e) {
			//表单验证
			var input = e.target;
			try {
				input.value = $.trim(input.value);
			} catch (ex) {
				//...
			}
			//html5验证
			input.checkValidity();
		});

		//点击文档中的任意文章，均隐藏模拟的右键菜单
		$(document).click(function() {
			contextMenu.hide();
		});
		//文件添加
		$(":file[accept*='image']").each(addfile).change(addfile);
		//添加新图片点击
		$(".ui_file button").click(function() {
			$(this.form || document).find(":file").click();
		});

		//简繁切换
		$(".buttonset [name='lang']").change(function() {
			lang = this.value;

			//根据模块添加语言相关的class
			rootMod.removeClass("lang_sc").removeClass("lang_tw").addClass("lang_" + lang);
			$.each(dataCache, function(i, obj) {
				obj.update();
			});
		}).filter(":checked").change();

		//pc端和移动端切换，加载不同的css文件
		$(".buttonset [name='mode']").change(function() {
			var platform = this.value,
				platformLink = $(".platform_link"),
				href = platformLink.attr("href"),
				newUrl;
			newUrl = (platform === "mob") ? href.replace(/pagetpl/gi, "mpagetpl") : href.replace(/mpagetpl/gi, "pagetpl");
			platformLink.attr("href", newUrl);
		});
		//阻止浏览器图片的默认拖放事件
		$(document).on("dragstart", "img", function() {
			return false;
		});
		//清空页面
		$(".empty").click(function() {
			//移除根模块
			rootMod.empty().remove();
		});

		//组件插入
		$("li[data-type]", ".component").on("click", function(e) {
			var type = $(this).attr("data-type"),
				pagetpl = $(".pagetpl");
			e.preventDefault();
			if (type === "pagetpl") {
				//如果当前模块没有pagetpl根模块，则插入根模块，并设置为当前可编辑状态
				if (!pagetpl.length) {
					rootMod = new Module(insertData).node().insertBefore(".dev_bar");
					setActivity();
				}
			} else {
				//调用插入组件函数
				insertComponent(insertData, type);
			}
		});

			//点击保存
		$(".save_btn").click(function() {
			var activityId = $("input[name='activityId']").val(), //activity id
				data = getData(rootMod).dataObj,
				dom = updateHTML(getHTML(data).node()), //dom对象
				html = resgiterJsCss + $("<div>").append(dom).html(), //html文本
				formData = new FormData();

			// 临时硬编码方案，下次改为<style>标签方式
			html = html.replace(/-webkit-([^;]+)\s*;\s*-webkit-\1/g, "-webkit-$1; $1").replace(/-moz-(\w+(-\w+)*:)/g, "$1");


			$(".simulate_area").each(function() {
				var coords = $(this).attr("data-coords").split(","),
					usemap = $(this).attr("data-usemap"),
					styles = $(this).attr("style"),
					href = $(this).attr("href");

				var obj = {
					type: "simulate_area",
					coords: coords,
					usemap :usemap,
					style: styles,
					href: href
				};
				//append obj
				data.children.push(obj);
			});

			data = JSON.stringify(data);
			//向formData追加数据
			$.each(filesCache, function(url, file) {
				if (data.indexOf(url) > -1) {
					formData.append(url, file);
				}
			});
			formData.append("activityId", activityId);
			formData.append("data", data);
			formData.append("html", html);
			formData.append("lang", lang);

			$.ajax({
				url: "/activity/saveCms",
				type: "post",
				dataType: "json",
				data: formData,
				processData: false,
				contentType: false
			}).done(function(data) {
				//状态提示
				if (data.result === "1") {
					window.alert("save success");
				} else {
					window.alert("save false");
				}
				//重置saveStatus
				saveStatus = false;
				//刷新页面
				window.location.reload();
			}).fail(function() {
				window.alert("save error");
			});

		});

	});
});