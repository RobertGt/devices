var WEBHTTP = "http://192.168.31.247/";
(function(){
	$.FormObject = function (title,table,ajaxUrl,object,option){
		//编辑表名
		var url = ajaxUrl;
		var pageSize = 15;
		var pageNum = 1;
		var totalPage = 0;
		this.run = function(){
			var html = "";
			$.each(title,function(i){
				html+="<th data-locale=" + title[i] + ">" + title[i] + "</th>";
			})
			$(object).find("thead tr").html(html);
            loadLang();
			getDatas(pageNum);
		}
        var lists =  function(data, response){
            setTable(response.result.row);
            setPage(pageNum,pageSize,response.result.total);
        }
		var request = function (ajaxUrl, method, data, callback) {
            layer.load(0, {shade: [0.1,'#000']});
            $.ajax({
                headers: {
                    lang: $.cookie('lang'),
                    token: $.cookie('login_token')
                },
                type : method,
                url : ajaxUrl,
                data : data,
                async : false,
                success : function(response){
                	if(response.error == 0){
                        callback(data, response);
					}else if(response.error == 401){
                        toastr.error(response.reason);
                        setTimeout(function(){
                            $.cookie('login_token', '', {path: '/', expires: -1});
                            $.cookie('login_account', '', {path: '/', expires: -1});
                            location.href = "../../index.html"
						},500);
					}else {
                        toastr.error(response.reason);
					}
                    setTimeout(function(){layer.closeAll();},100);
                },
                error :function(response){
                    layer.closeAll();
                    toastr.error('加载失败');
                }
            });
        }
		var getDatas = function(pageNums){
            pageNum = pageNums;
			var postData = {pageSize:pageSize,pageNum:pageNums}
            request(WEBHTTP + ajaxUrl, 'GET', postData, lists)
		}

		var setTable = function(datas){
			var html = "";
			$.each(datas,function(v){
				html+="<tr>"
				$.each(table,function(i){
					if(table[i].type == "input"){
						html+="<td style='width: 5%;'><div class='col-md-12 text-align' style='padding: 0px;'><input data-id="+datas[v].id+" value='"+datas[v][table[i].name]+"' class='form-control sortlist'></div></td>"
					}else if(table[i].type == "url"){
						if(datas[v][table[i].url]){
							html+="<td><a target='_blank' href='"+datas[v][table[i].url]+"' title='点击跳转'>" + datas[v][table[i].name] + "</a></td>"
						}else{
							html+="<td>" + datas[v][table[i].name] + "</td>"
						}
					}else if(table[i].type == "theme"){
                        html+="<td><div class='themeInfo' style='background: " + datas[v][table[i].name] +  " '></div></td>"
					}else if(table[i].type == "image"){
                        html+='<td style="width: 15%;"><a class="fancybox" href="' + datas[v][table[i].name] + '" title=""><img src="' + datas[v][table[i].name] + '" class="attachments"/></a></td>'
                    }else if(table[i].type == "audio"){
                        html+="<td><audio src='" + datas[v][table[i].name] + "' controls='controls'></audio></td>"
					}else if(table[i].type =="select"){
						if(datas[v][table[i].name] == 0){
                            html +="<td><a href='javascript:;' class='publish' data-id="+datas[v].id+">" + table[i].select[datas[v][table[i].name]] + "</a></td>"
						}else{
                            html +="<td><a href='javascript:;' class='cancel' data-id="+datas[v].id+">" + table[i].select[datas[v][table[i].name]] + "</a></td>";
						}
					}else{
						if(table[i].width){
							html+="<td style='width: "+table[i].width+";'>" + datas[v][table[i].name] + "</td>"
						}else{
							html+="<td>" + datas[v][table[i].name] + "</td>"
						}
					}
				})
				if(option.length > 0){
				html+='<td class="operation">';
					$.each(option,function(i){
                        if(option[i] == "send"){
                            html+=' <button class="btn btn-primary btn-sm" type="button"  data-id='+datas[v].id+'><i class="fa fa-send"></i> <span class="bold" data-locale="send"> 指令发送</span></button> '
                        }else if(option[i] == "statistics"){
                            html+=' <button class="btn btn-info btn-sm" type="button"  data-id='+datas[v].id+'><i class="fa fa-bar-chart"></i> <span class="bold" data-locale="statistics"> 统计</span></button> '
                        }else if(option[i] == "allot"){
                            html+=' <button class="btn btn-warning btn-sm" type="button"  data-id='+datas[v].id+'><i class="fa fa-user-plus"></i> <span class="bold" data-locale="allot"> 权限分配</span></button> '
                        }else if(option[i] == "edit"){
							html+=' <button class="btn btn-success btn-sm" type="button"  data-id='+datas[v].id+'><i class="fa fa-edit"></i> <span class="bold" data-locale="edit"> 修改</span></button> '
						} else if(option[i] == "del"){
							html+=' <button class="btn btn-danger btn-sm" type="button"  data-id='+datas[v].id+'><i class="fa fa-trash-o"></i> <span class="bold" data-locale="del"> 删除</span></button> '
						}
					})
					html+="</td>"
				}
				html+="</tr>"
			})
			$(object).find("tbody").html(html);
            loadLang();
		}
		var setPage = function(nowPage,pageSize,totalRows){
			var html = pageShow(nowPage,pageSize,totalRows);
			$(object).find("#editable_paginate").html(html);
		}
		var pageShow = function(nowPage,pageSize,totalRows){
			if(totalRows<=0)return "";
			var totalPages = Math.ceil(totalRows /pageSize);
			var rollPage   = 11;// 分页栏每页显示的页数

			/* 计算分页临时变量 */
			var nowCoolPage  = rollPage/2;

			var nowCoolPageCeil = Math.ceil(nowCoolPage);
			//上一页
			var upRow = nowPage-1;
			var upPage = upRow > 0 ?'<li><a class="prev" onclick="object.jumps('+upRow+')">← 上一页</a></li>' : '';

			//下一页
			var downRow  = parseInt(nowPage) + 1;
			var downPage = (downRow <= totalPages) ? '<li><a class="next" onclick="object.jumps('+downRow+')">下一页 →</a></li>' : '';

			//第一页
			var theFirst = '';
			if(totalPages > rollPage && (nowPage - nowCoolPage) >= 1){
				theFirst = '<li><a class="first"  onclick="object.jumps(1)">1...</a></li>';
			}
			//最后一页
			var theEnd = '';
			if(totalPages > rollPage && (parseInt(nowPage) + parseInt(nowCoolPage)) < totalPages){
				var theEnd = '<li><a class="end" onclick="object.jumps('+totalPages+')">'+totalPages+'</a></li>';
			}
			var linkPage = "";
			//数字连接
			for(var i = 1; i <= rollPage; i++){
				if((nowPage - nowCoolPage) <= 0 ){
					var page = i;
				}else if((parseInt(nowPage) + parseInt(nowCoolPage) - 1) >= totalPages){
					var page = parseInt(totalPages) - parseInt(rollPage) + parseInt(i);
				}else{
					var page = parseInt(nowPage) - parseInt(nowCoolPageCeil) + parseInt(i);
				}
				if(page > 0 && page != nowPage){
					if(page <= totalPages){
						linkPage += '<li><a class="num" onclick="object.jumps('+page+')">' +page+ '</a></li>';
					}else{
						break;
					}
				}else{
					if(page > 0 && totalPages != 1){
						linkPage += '<li class="active"><a page="'+page+'">' +page+ '</a></li>';
					}
				}
			}
			return "<ul class='pagination'>"+theFirst+upPage+linkPage+downPage+theEnd+"</ul>";
		}
		this.jumps = function(pageNum){
			getDatas(pageNum);
		}
		this.getPageNum = function(){
			return pageNum;
		}
		this.add = function(url,title){
			layer.open({
				  type: 2,
				  title: title,
				  shadeClose: true,
				  shade: 0.8,
				  area: ['100%', '100%'],
				  content: url
			});
		}
		this.order = function(url,object){
			layer.load(0, {shade: [0.1,'#000']});
			var postData = {order:object}
			$.ajax({
					headers: {
                        lang: $.cookie('lang'),
						token: $.cookie('login_token')
					},
				 type : "POST",
				 url : url,
				 data : postData,
				 async : false,
				 success : function(response){
					layer.closeAll();
					if(response.error == 0){
						getDatas(pageNum);
					}else if(response.error == 401){
                        toastr.error(response.reason);
                        setTimeout(function(){
                            $.cookie('login_token', '', {path: '/', expires: -1});
                            $.cookie('login_account', '', {path: '/', expires: -1});
                            location.href = "../../index.html"
                        },500);
                    }else{
                        toastr.error('操作失败');
					}
				 },
				error :function(response){
					layer.closeAll();
                    toastr.error('请求失败');
				}
			});
		}
		this.del = function(url,id){
			layer.confirm("删除信息将无法恢复,确认删除?", {
				btn: ["确定", "取消"]
			}, function() {
                postData = {id:id};
                request(WEBHTTP + url, 'GET', postData, function(){
                    getDatas(pageNum);
				})
			})
		}
		this.edit = function(url,id,title){
            updateId = id;
			layer.open({
				  type: 2,
				  title: title,
				  shadeClose: true,
				  shade: 0.8,
				  area: ['100%', '100%'],
				  content: url
			});
		}
		this.search = function(url,object){
			pageNum = 1;
			var postData = {pageSize:pageSize,pageNum:pageNum,seach:object}
            request(WEBHTTP + url, 'GET', postData, lists)
		}
		//返回函数本身对象与base对象合并体
		return this;
	};
})(jQuery);
function loadLang() {
    $.i18n.properties({
        name:'strings',
        path: typeof i18n == 'undefined' ? '../../static/js/i18n/' : i18n,
        mode:'map',
        language: $.cookie('lang'),
        callback:function(){
            $("[data-locale]").each(function(){
                $(this).html($.i18n.prop($(this).data("locale")));
            });
            $("[data-placeholder]").each(function(){
                $(this).attr('placeholder', $.i18n.prop($(this).data("placeholder")));
            });
        }
    });
}
loadLang();
