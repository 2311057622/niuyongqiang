/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : 小学生wyj
 * @date : 2017年5月23日 19:55:01
 * @version : v1.0.0.0
 * 运费模版添加
 */
$(function(){
	
	//判断是否显示
	$(".trigger").click(function () {
		var item = $(".province-list .ecity .gareas input");
		var province_list = $(".province-list .ecity");
		if ($(this).parent().next().css("display") == "block") {
			
			for (var i = 0; i < item.length; i++) {
				$("#citys_" + item[i].value + "").css("display", "none");
			}
			
			for (var i = 0; i < province_list.length; i++) {
				$(province_list[i]).removeClass("showCityPop");
			}
			
			$(this).parent().next().css("display", "none");
			$(this).parent().parent().removeClass("showCityPop");
			
		} else {
			
			for (var i = 0; i < item.length; i++) {
				$("#citys_" + item[i].value + "").css("display", "none");
			}
			
			for (var i = 0; i < province_list.length; i++) {
				$(province_list[i]).removeClass("showCityPop");
			}
			$(this).parent().parent().addClass("showCityPop");
			$(this).parent().next().css("display", "block")
		}
	})
	
	//关闭按钮
	$(".close_button").click(function () {
		$(this).parent().parent().css("display", "none");
		$(this).parent().parent().parent().removeClass("showCityPop");
	});
	
	//全部省市华东
	$("[name='J_Group_0_Province']").each(function () {
		$("[name='J_Group_0_Province']").change(function () {
			if ($(this).attr("checked") === "checked") {
				var citys = $("#citys_" + $(this).val() + " .areas input");
				for (var i = 0; i < citys.length; i++) {
					if ($(citys[i]).attr("disabled") === undefined || $(citys[i]).attr("disabled") === false) {
						$(citys[i]).attr("checked", true);
					}
				}
			} else { $("#citys_" + $(this).val() + " .areas input").attr("checked", false); }
		})
	});
	
	//全部省市华北
	$("[name='J_Group_1_Province']").each(function () {
		$(this).change(function () {
			if ($(this).attr("checked") === "checked") {
				var citys = $("#citys_" + $(this).val() + " .areas input");
				for (var i = 0; i < citys.length; i++) {
					if ($(citys[i]).attr("disabled") === undefined || $(citys[i]).attr("disabled") === false) {
						$(citys[i]).attr("checked", true);
					}
				}
			} else { $("#citys_" + $(this).val() + " .areas input").attr("checked", false); }
		});
	});
	
	//全部省市华中
	$("[name='J_Group_2_Province']").each(function () {
		$(this).change(function () {
			if ($(this).attr("checked") === "checked") {
				var citys = $("#citys_" + $(this).val() + " .areas input");
				for (var i = 0; i < citys.length; i++) {
					if ($(citys[i]).attr("disabled") === undefined || $(citys[i]).attr("disabled") === false) {
						$(citys[i]).attr("checked", true);
					}
				}
			} else { $("#citys_" + $(this).val() + " .areas input").attr("checked", false); }
		});
	});
	
	//全部省市华南
	$("[name='J_Group_3_Province']").each(function () {
		$(this).change(function () {
			if ($(this).attr("checked") === "checked") {
				var citys = $("#citys_" + $(this).val() + " .areas input");
				for (var i = 0; i < citys.length; i++) {
					if ($(citys[i]).attr("disabled") === undefined || $(citys[i]).attr("disabled") === false) {
						$(citys[i]).attr("checked", true);
					}
				}
			} else { $("#citys_" + $(this).val() + " .areas input").attr("checked", false); }
		});
	});
	
	//全部省市东北
	$("[name='J_Group_4_Province']").each(function () {
		$(this).change(function () {
			if ($(this).attr("checked") === "checked") {
				var citys = $("#citys_" + $(this).val() + " .areas input");
				for (var i = 0; i < citys.length; i++) {
					if ($(citys[i]).attr("disabled") === undefined || $(citys[i]).attr("disabled") === false) {
						$(citys[i]).attr("checked", true);
					}
				}
			} else { $("#citys_" + $(this).val() + " .areas input").attr("checked", false); }
		});
	});
	
	//全部省市西北
	$("[name='J_Group_5_Province']").each(function () {
		$(this).change(function () {
			if ($(this).attr("checked") === "checked") {
				var citys = $("#citys_" + $(this).val() + " .areas input");
				for (var i = 0; i < citys.length; i++) {
					if ($(citys[i]).attr("disabled") === undefined || $(citys[i]).attr("disabled") === false) {
						$(citys[i]).attr("checked", true);
					}
				}
			} else { $("#citys_" + $(this).val() + " .areas input").attr("checked", false); }
		});
	});
	
	//全部省市西南
	$("[name='J_Group_6_Province']").each(function () {
		$(this).change(function () {
			if ($(this).attr("checked") === "checked") {
				var citys = $("#citys_" + $(this).val() + " .areas input");
				for (var i = 0; i < citys.length; i++) {
					if ($(citys[i]).attr("disabled") === undefined || $(citys[i]).attr("disabled") === false) {
						$(citys[i]).attr("checked", true);
					}
				}
			} else { $("#citys_" + $(this).val() + " .areas input").attr("checked", false); }
		});
	});
	
	//全部省市港澳台
	$("[name='J_Group_7_Province']").each(function () {
		$(this).change(function () {
			if ($(this).attr("checked") === "checked") {
				var citys = $("#citys_" + $(this).val() + " .areas input");
				for (var i = 0; i < citys.length; i++) {
					if ($(citys[i]).attr("disabled") === undefined || $(citys[i]).attr("disabled") === false) {
						$(citys[i]).attr("checked", true);
					}
				}
			} else { $("#citys_" + $(this).val() + " .areas input").attr("checked", false); }
		});
	});
	
	//华东
	$("[name='J_Group_0']").change(function () {
		if ($(this).attr("checked") === "checked") {
			$("[name='J_Group_0_Province']").each(function () {
				if ($(this).attr("disabled") == undefined || $(this).attr("disabled") == false) {
					$(this).attr("checked", true);
				} else { $(this).attr("checked", false); }
				//全部省市
				$("[name='J_Group_0_Province']").each(function () {
					$("#citys_" + $(this).val() + " .areas input").each(function () {
						if ($(this).attr("disabled") == undefined || $("#citys_" + $(this).val() + " .areas input").attr("disabled") == false) {
							$(this).attr("checked", true);
						} else { $(this).attr("checked", false); }
					});
				})
			})
		} else {
			$("[name='J_Group_0_Province']").attr("checked", false);
			//全部省市
			$("[name='J_Group_0_Province']").each(function () {
				$("#citys_" + $(this).val() + " .areas input").attr("checked", false);
			});
		}
	});
	
	//华北
	$("[name='J_Group_1']").change(function () {
		if ($(this).attr("checked") === "checked") {
			$("[name='J_Group_1_Province']").each(function () {
				if ($(this).attr("disabled") == undefined || $(this).attr("disabled") == false) {
					$(this).attr("checked", true);
				} else { $(this).attr("checked", false); }
				//全部省市
				$("[name='J_Group_1_Province']").each(function () {
					$("#citys_" + $(this).val() + " .areas input").each(function () {
						if ($(this).attr("disabled") == undefined || $("#citys_" + $(this).val() + " .areas input").attr("disabled") == false) {
							$(this).attr("checked", true);
						} else { $(this).attr("checked", false); }
					});
				})
			})
		} else {
			$("[name='J_Group_1_Province']").attr("checked", false);
			//全部省市
			$("[name='J_Group_1_Province']").each(function () {
				$("#citys_" + $(this).val() + " .areas input").attr("checked", false);
			});
		}
	});
	
	//华中
	$("[name='J_Group_2']").change(function () {
		if ($(this).attr("checked") === "checked") {
			$("[name='J_Group_2_Province']").each(function () {
				if ($(this).attr("disabled") == undefined || $(this).attr("disabled") == false) {
					$(this).attr("checked", true);
				} else { $(this).attr("checked", false); }
				//全部省市
				$("[name='J_Group_2_Province']").each(function () {
					$("#citys_" + $(this).val() + " .areas input").each(function () {
						if ($(this).attr("disabled") == undefined || $("#citys_" + $(this).val() + " .areas input").attr("disabled") == false) {
							$(this).attr("checked", true);
						} else { $(this).attr("checked", false); }
					});
				})
			})
		} else {
			$("[name='J_Group_2_Province']").attr("checked", false);
			//全部省市
			$("[name='J_Group_2_Province']").each(function () {
				$("#citys_" + $(this).val() + " .areas input").attr("checked", false);
			});
		}
	});
	
	//华南
	$("[name='J_Group_3']").change(function () {
		if ($(this).attr("checked") === "checked") {
			$("[name='J_Group_3_Province']").each(function () {
				if ($(this).attr("disabled") == undefined || $(this).attr("disabled") == false) {
					$(this).attr("checked", true);
				} else { $(this).attr("checked", false); }
				//全部省市
				$("[name='J_Group_3_Province']").each(function () {
					$("#citys_" + $(this).val() + " .areas input").each(function () {
						if ($(this).attr("disabled") == undefined || $("#citys_" + $(this).val() + " .areas input").attr("disabled") == false) {
							$(this).attr("checked", true);
						} else { $(this).attr("checked", false); }
					});
				})
			})
		} else {
			$("[name='J_Group_3_Province']").attr("checked", false);
			//全部省市
			$("[name='J_Group_3_Province']").each(function () {
				$("#citys_" + $(this).val() + " .areas input").attr("checked", false);
			});
		}
	});
	
	//东北
	$("[name='J_Group_4']").change(function () {
		if ($(this).attr("checked") === "checked") {
			$("[name='J_Group_4_Province']").each(function () {
				if ($(this).attr("disabled") == undefined || $(this).attr("disabled") == false) {
					$(this).attr("checked", true);
				} else { $(this).attr("checked", false); }
				//全部省市
				$("[name='J_Group_4_Province']").each(function () {
					$("#citys_" + $(this).val() + " .areas input").each(function () {
						if ($(this).attr("disabled") == undefined || $("#citys_" + $(this).val() + " .areas input").attr("disabled") == false) {
							$(this).attr("checked", true);
						} else { $(this).attr("checked", false); }
					});
				})
			})
		} else {
			$("[name='J_Group_4_Province']").attr("checked", false);
			//全部省市
			$("[name='J_Group_4_Province']").each(function () {
				$("#citys_" + $(this).val() + " .areas input").attr("checked", false);
			});
		}
	});
	
	//西北
	$("[name='J_Group_5']").change(function () {
		if ($(this).attr("checked") === "checked") {
			$("[name='J_Group_5_Province']").each(function () {
				if ($(this).attr("disabled") == undefined || $(this).attr("disabled") == false) {
					$(this).attr("checked", true);
				} else { $(this).attr("checked", false); }
				//全部省市
				$("[name='J_Group_5_Province']").each(function () {
					$("#citys_" + $(this).val() + " .areas input").each(function () {
						if ($(this).attr("disabled") == undefined || $("#citys_" + $(this).val() + " .areas input").attr("disabled") == false) {
							$(this).attr("checked", true);
						} else { $(this).attr("checked", false); }
					});
				})
			})
		} else {
			$("[name='J_Group_5_Province']").attr("checked", false);
			//全部省市
			$("[name='J_Group_5_Province']").each(function () {
				$("#citys_" + $(this).val() + " .areas input").attr("checked", false);
			});
		}
	});
	
	//西南
	$("[name='J_Group_6']").change(function () {
		if ($(this).attr("checked") === "checked") {
			$("[name='J_Group_6_Province']").each(function () {
				if ($(this).attr("disabled") == undefined || $(this).attr("disabled") == false) {
					$(this).attr("checked", true);
				} else { $(this).attr("checked", false); }
				//全部省市
				$("[name='J_Group_6_Province']").each(function () {
					$("#citys_" + $(this).val() + " .areas input").each(function () {
						if ($(this).attr("disabled") == undefined || $("#citys_" + $(this).val() + " .areas input").attr("disabled") == false) {
							$(this).attr("checked", true);
						} else { $(this).attr("checked", false); }
					});
				})
			})
		} else {
			$("[name='J_Group_6_Province']").attr("checked", false);
			//全部省市
			$("[name='J_Group_6_Province']").each(function () {
				$("#citys_" + $(this).val() + " .areas input").attr("checked", false);
			});
		}
	});
	
	//港台澳
	$("[name='J_Group_7']").change(function () {
		if ($(this).attr("checked") === "checked") {
			$("[name='J_Group_7_Province']").each(function () {
				if ($(this).attr("disabled") == undefined || $(this).attr("disabled") == false) {
					$(this).attr("checked", true);
				} else { $(this).attr("checked", false); }
				//全部省市
				$("[name='J_Group_7_Province']").each(function () {
					$("#citys_" + $(this).val() + " .areas input").each(function () {
						if ($(this).attr("disabled") == undefined || $("#citys_" + $(this).val() + " .areas input").attr("disabled") == false) {
							$(this).attr("checked", true);
						} else { $(this).attr("checked", false); }
					});
				})
			})
		} else {
			$("[name='J_Group_7_Province']").attr("checked", false);
			//全部省市
			$("[name='J_Group_7_Province']").each(function () {
				$("#citys_" + $(this).val() + " .areas input").attr("checked", false);
			});
		}
	})
	
	
})

var flag = false;//防止重复提交
function TransportationAdd() {
	var shipping_fee_name = $("#TextName").val();
	if ($("#TextName").val() == "") {
		$(".prompt", $("#TextName").parent().parent()).text("请输入运费模板");
		$("#TextName").focus();
		return;
	}
	var shipping_fee_ext = "";
	if($("#PressPaper").html() != undefined){
		if ($("#NumberStartStandard").val() == "") {
			$(".prompt").text("");
			$("#spMessage").text("请输入默认运费");
			$("#NumberStartStandard").focus();
			return;
		} else if (isNaN($("#NumberStartStandard").val())) {
			$(".prompt").text("");
			$("#spMessage").text("请输入数字类型");
			$("#NumberStartStandard").focus();
			return;
		} else if ($("#NumberStartFee").val() == "") {
			$(".prompt").text("");
			$("#spMessage").text("请输入费用");
			$("#NumberStartFee").focus();
			return;
		} else if (isNaN($("#NumberStartFee").val())) {
			$(".prompt").text("");
			$("#spMessage").text("请输入数字类型");
			$("#NumberStartFee").focus();
			return;
		} else if ($("#NumberAddStandard").val() == "") {
			$(".prompt").text("");
			$("#spMessage").text("请输入续件数");
			$("#NumberAddStandard").focus();
			return;
		} else if (isNaN($("#NumberAddStandard").val())) {
			$(".prompt").text("");
			$("#spMessage").text("请输入数字类型");
			$("#NumberAddStandard").focus();
			return;
		} else if ($("#NumberAddFee").val() == "") {
			$(".prompt").text("");
			$("#spMessage").text("请输入续费");
			$("#NumberAddFee").focus();
			return;
		} else if (isNaN($("#NumberAddFee").val())) {
			$(".prompt").text("");
			$("#spMessage").text("请输入数字类型");
			$("#NumberAddFee").focus();
			return;
		}
		var shipping_fee_ext=$.trim(""+";"+""+";"+$("#NumberStartStandard").val()+";"+$("#NumberStartFee").val()+";"+
		$("#NumberAddStandard").val()+";"+$("#NumberAddFee").val()+";1"+"|");
	}
	
	if ($("#tbl-except").css("display") == "block") {
		var Rowsindex = 0;
		var IsNull = true;
		$("#myPressPaperTable tr").not("tr:eq(0)").each(function () {
			Rowsindex++;
			if ($("[name='furniture_areas_n" + Rowsindex + "']").val() == "") {
				$(".prompt").text("");
				$("#spMessage").text("请选择地区");
				IsNull = false;
			} else if ($("[name='StartStandard" + Rowsindex + "']").val() == "") {
				$(".prompt").text("");
				$("#spMessage").text("请输入文本");
				$("[name='StartStandard" + Rowsindex + "']").focus();
				IsNull = false;
			} else if (isNaN($("[name='StartStandard" + Rowsindex + "']").val())) {
				$(".prompt").text("");
				$("#spMessage").text("请输入数字类型");
				$("[name='StartStandard" + Rowsindex + "']").focus();
				IsNull = false;
			} else if ($("[name='StartFee" + Rowsindex + "']").val() == "") {
				$(".prompt").text("");
				$("#spMessage").text("请输入文本");
				$("[name='StartFee" + Rowsindex + "']").focus();
				IsNull = false;
			} else if (isNaN($("[name='StartFee" + Rowsindex + "']").val())) {
				$(".prompt").text("");
				$("#spMessage").text("请输入数字类型");
				$("[name='StartFee" + Rowsindex + "']").focus();
				IsNull = false;
			} else if ($("[name='StartAddStandard" + Rowsindex + "']").val() == "") {
				$(".prompt").text("");
				$("#spMessage").text("请输入文本");
				$("[name='StartAddStandard" + Rowsindex + "']").focus();
				IsNull = false;
			} else if (isNaN($("[name='StartAddStandard" + Rowsindex + "']").val())) {
				$(".prompt").text("");
				$("#spMessage").text("请输入数字类型");
				$("[name='StartAddStandard" + Rowsindex + "']").focus();
				IsNull = false;
			} else if ($("[name='StartAddFee" + Rowsindex + "']").val() == "") {
				$(".prompt").text("");
				$("#spMessage").text("请输入文本");
				$("[name='StartAddFee" + Rowsindex + "']").focus();
				IsNull = false;
			} else if (isNaN($("[name='StartAddFee" + Rowsindex + "']").val())) {
				$(".prompt").text("");
				$("#spMessage").text("请输入数字类型");
				$("[name='StartAddFee" + Rowsindex + "']").focus();
				IsNull = false;
			}
		});
		var num=0;
		if (IsNull) {
			$("#myPressPaperTable tr").not("tr:eq(0)").each(function () {
				num ++;
				shipping_fee_ext +=$.trim($("[name='furniture_areas_name_n"+ num+"']").val()+";"
				+$("[name='StartStandard"+num+"']").val()+";"+$("[name='StartFee" + num + "']").val()
				+";"+$("[name='StartAddStandard" + num + "']").val()+";"+$("[name='StartAddFee" + num + "']").val()+";0|");
			})
		}else { return; }
	}
	if(shipping_fee_ext == ''){
		return;
	}
	shipping_fee_ext= shipping_fee_ext.substring(0, shipping_fee_ext.length - 1);
	if(flag){
		return;
	}
	flag = true;
	$.ajax({
		type : "post",
		url : ADMINMAIN+"/Express/TransportationAdd",
		async : true,
		data : {
			"shipping_fee_name" : shipping_fee_name,
			"shipping_fee_ext" : shipping_fee_ext
		},
		success : function(data) {
			if (data["code"] > 0) {
				showMessage('success', "运费模板添加成功",ADMINMAIN+'/Express/Transportation');
			}else{
				showMessage('error', "运费模板添加失败");
				flag = false;
			}	
		}
 })

}

//地区选择点击确定
function makeSure() {
	//$("makeSureItem").innerHTML = $("previewItem").innerHTML;
	var makeSure = $("#hidMakeSure").val()
	var hidArea = $("#hidArea").val();
	var hidAreaName = $("#hidAreaName").val();
	var hidProvince = $("#hidProvince").val();
	//添加地区显示到表格
	var items = $("#J_CityList .clearfix .province-list :input[type='checkbox']");
	var province_values = "";
	var city_values = "";
	var values = "";
	var text = "";
	for (var i = 0; i < items.length; i++) {
		if (items[i].checked === true) {
			var attr_class = $(items[i]).attr("class");
			if(attr_class =="J_Province"){
				values += items[i].value + ","; 
				province_values +=items[i].value + ",";
			}else{
				city_values +=items[i].value + ",";
				values += $(items[i]).data("value_id") + ","; 
			}
		text += items[i].lang + "、";
		}
	}
	address_value=province_values.substring(0, province_values.length - 1)+";"+city_values.substring(0, city_values.length - 1);
	if (text != "") {
		$("#" + makeSure + "").parent().parent().prev("div").text(text.substring(0, text.length - 1));
		$("[name='" + hidAreaName + "']").val(address_value);
		$("[name='" + hidArea + "']").val(values.substring(0, values.length - 1));
	}
	openBg(0, "event");
	openSelect(0);
}

//添加行
function InsertRow(){
	var tag = "checkbox04";
	$("#initialWorkpiece").text("首件(件)");
	$("#collectFees").text("首费(元)");
	$("#Continued").text("续件(件)");
	$("#Renew").text("续费(元)");
	$("#tbl-except").css("display", "block");
	//<a href="" class="acc_popup edit J_EditArea" style="float: right;color: #3366CC;" data-acc="event:enter" area-controls="J_DialogArea"   area-haspopup="true" >编辑</a><div class="area-group"><p>未添加地区</p></div>
	var tableRow = document.getElementById("myPressPaperTable");
	var row_index = tableRow.rows.length;
	var trRow = tableRow.insertRow(row_index);
	trRow.id = "trRow" + row_index;
	var cel1 = trRow.insertCell(0);
	cel1.innerHTML = '<div> 未添加地区</div> <div class="mod-operate float-r"><div class="con style0editdel" style="float:right;"> <a  id="edit' + row_index + '"  onclick="openBg(1,event);openSelect(1);" title="编辑运送区域" class="del" href="javascript:void(0)">编辑</a></div></div><input type="hidden" name="furniture_areas_n' + row_index + '"><input type="hidden" name="furniture_areas_name_n' + row_index + '">';
	var cel2 = trRow.insertCell(1);
	cel2.innerHTML = "<input id='StartStandard_" + row_index + "' class='w30' type='text' value='1' name='StartStandard" + row_index + "' />";
	var cel3 = trRow.insertCell(2);
	cel3.innerHTML = "<input id='StartFee_" + row_index + "' class='w30' type='text' name='StartFee" + row_index + "'/>";
	var cel4 = trRow.insertCell(3);
	cel4.innerHTML = "<input id='StartAddStandard_" + row_index + "' class='w30' type='text' value='1' name='StartAddStandard" + row_index + "' />";
	var cel5 = trRow.insertCell(4);
	cel5.innerHTML = "<input id='StartAddFee_" + row_index + "' class='w30' type='text' name='StartAddFee" + row_index + "'/>";
	var cel6 = trRow.insertCell(5);
	cel6.innerHTML = "<div class='mod-operate'><div class='con style0editdel'><a name='de_" + row_index + "'  class='del' href='javascript:void(0)'  onclick=\"dlRow('trRow" + row_index + "')\"> 删除</a></div> </div>";
	var Rowsindex = 0;
	$(function(){
		$('#tablerow').val(row_index);
	})
	
	$("#myPressPaperTable tr").not("tr:eq(0)").each(function () {
		Rowsindex++;
		ISinputString("#StartStandard_" + Rowsindex + "");
		ISinputString("#StartFee_" + Rowsindex + "");
		ISinputString("#StartAddStandard_" + Rowsindex + "");
		ISinputString("#StartAddFee_" + Rowsindex + "");
	});
}


//删除行
function dlRow(RowId) {
	var itemsCheckbox = $("#J_CityList :input[type='checkbox']");
	var deleteRow = document.getElementById(RowId).rowIndex;
	var items = "";
	var item = "";
	items = $("#edit" + deleteRow + "").parents("td").find("input[type='hidden']").val();
	document.getElementById("myPressPaperTable").deleteRow(deleteRow);
	$('#tablerow').val($('#tablerow').val()-1);
	//多选框解锁
	item = items.split(',');
	for (var i = 0; i < item.length; i++) {
		for (var j = 0; j < itemsCheckbox.length; j++) {
			if (item[i] == itemsCheckbox[j].value) {
				itemsCheckbox[j].disabled = false;
			}
		}
	}
	//重新设置ID
	var index = 0;
	$("#myPressPaperTable tr:not(:first)").each(function () {
		index++;
		$("td:first a", $(this)).attr("id", "edit" + index);
		$("td:first input:eq(0)", $(this)).attr("name", "furniture_areas_n" + index);
		$("td:first input:eq(1)", $(this)).attr("name", "furniture_areas_name_n" + index);
		$("td:eq(1) input", $(this)).attr("name", "StartStandard" + index);
		$("td:eq(2) input", $(this)).attr("name", "StartFee" + index);
		$("td:eq(3) input", $(this)).attr("name", "StartAddStandard" + index);
		$("td:eq(4) input", $(this)).attr("name", "StartAddFee" + index);
		$("td:eq(1) input", $(this)).attr("id", "StartStandard_" + index);
		$("td:eq(2) input", $(this)).attr("id", "StartFee_" + index);
		$("td:eq(3) input", $(this)).attr("id", "StartAddStandard_" + index);
		$("td:eq(4) input", $(this)).attr("id", "StartAddFee_" + index);
	})
}

//编辑
function openBg(state, event) { //遮照打开关闭控制
	if (state == 1) {
		//显示加长空间
		$('.fill').css('display', 'block');
		//浏览器兼容
		event = event ? event : window.event;
		var eventSrc = event.srcElement ? event.srcElement : event.target;
		$("#hidMakeSure").val($(eventSrc).attr("id"));
		$("#hidArea").val($(eventSrc).parents("td").find("input").attr("name"));
		$("#hidAreaName").val($(eventSrc).parents("td").find("input").next().attr("name"));
		//获取当前所选择
		var itemsCheckboxs = $(eventSrc).parents("td").find("input").val();
		var itemCheckbox = "";
		//获取所有的记录选择地区的ID
		var itemHidden = "";
		var items = $("#J_CityList input[type='checkbox']");
		var itemsHidden = $("#myPressPaperTable input[type='hidden']:first");
		for (var i = 0; i < itemsHidden.length; i++) {
			itemHidden += itemsHidden[i].value + ",";
		}
		if (itemsHidden !== "") {
			//判断是否选择是就禁用
			var itemsHiddens = itemHidden.split(',');
			for (var j = 0; j < itemsHiddens.length; j++) {
				for (var i = 0; i < items.length; i++) {
					var attr_class = $(items[i]).attr("class");
					if(attr_class =="J_Province" ){ 
						if (itemsHiddens[j] == items[i].value) { 
							$(items[i]).attr("disabled", "disabled");
							$(items[i]).attr("checked", false);
						}
					}else if(attr_class =="J_City"){
						if(itemsHiddens[j] == $(items[i]).data("value_id")){
							$(items[i]).attr("disabled", "disabled");
							$(items[i]).attr("checked", false);
						}
					}
				}
			}
		}
		//判断是否是当前的选择如果是就解禁
		if (itemsCheckboxs !== undefined) {
			if (itemsCheckboxs !== "") {
				itemCheckbox = itemsCheckboxs.split(',');
				for (var i = 0; i < itemCheckbox.length; i++) {
					for (var j = 0; j < items.length; j++) {
						var attr_class = $(items[j]).attr("class");
						if(attr_class =="J_Province"){
							if (itemCheckbox[i] == items[j].value) { 
								$(items[j]).attr("checked", "checked");
								$(items[j]).attr("disabled", false);
							}
						}else if(attr_class =="J_City"){
							if(itemCheckbox[i] == $(items[j]).data("value_id")){
								$(items[j]).attr("checked", "checked");
								$(items[j]).attr("disabled", false);
							}
						}
					}
				}
			}
		}
		document.getElementById("bg").style.display = "block";
		var h = document.body.offsetWidth > document.documentElement.offsetWidth ? document.body.offsetWidth : document.documentElement.offsetWidth;
		//alert(document.body.offsetHeight);
		//alert(document.documentElement.offsetHeight);
		document.getElementById("bg").style.height = h + "px";
	} else {
		document.getElementById("bg").style.display = "none";
	}
}

function openSelect(state) { //选择城市层关闭打开控制
	if (state == 1) {
		document.getElementById("selectItem").style.display = "block";
		document.getElementById("selectItem").style.left = (document.getElementById("bg").offsetWidth - document.getElementById("selectItem").offsetWidth) / 7 + "px";
		document.getElementById("selectItem").style.top = document.body.scrollTop + 320 + "px";
	} else {
		document.getElementById("selectItem").style.display = "none";
		$("#J_CityList input[type='checkbox']").each(function () {
			if ($(this).attr("checked") == "checked") {
				$(this).attr("checked", false);
				$(this).attr("disabled", false);
			}
		})
	}
}

/**
*删除默认模板
*/
function deleteThisParentBox(event){
	$(event).parent().remove();
	$("#fee_box").prepend("<a onclick='addDefaultBox(this);'>添加默认模板</a>");
}

function addDefaultBox(event){
	$(event).remove();
	var html = '<div id="PressPaper" style="border:1px solid #e6e6e6;width:713px;padding:10px;">';
	html +='<span>默认运费：</span><input type="text" id="NumberStartStandard" name="StartStandard0" value="1" class="w100"><span id="defaultUnit">件</span> <span class="l15">费用：</span><input type="text" id="NumberStartFee" name="StartFee0" class="w100"><span id="defaultMoney">元</span>';
	html +='<span class="l15">每增加：</span><input type="text" id="NumberAddStandard" name="StartAddStandard0" class="w100"><span id="defaultAddUnit">件</span> <span class="l15">费用增加：</span><input type="text" id="NumberAddFee" name="StartAddFee0" class="w100"><span id="defaultAddMoney">元</span>';
	html +='<a  class="l15"style="float:right; cursor:pointerheight: 32px;line-height: 32px;" onclick="deleteThisParentBox(this);">删除</a>';
	html +='</div>';
	$("#fee_box").prepend(html);
}


//验证只能输入小数点和数字
function ISinputString(input) {
	$(input).keydown(function (event) {
		var keyCode = event.which;
		if (keyCode == 46 || keyCode == 8 || keyCode == 190 || (keyCode >= 48 && keyCode <= 57) || (keyCode >= 96 && keyCode <= 105) || keyCode == 110) {
			return true;
		} else { return false }
	}).focus(function () {
		this.style.imeMode = 'disabled';
	});
}

//文本框只能输入数字，不能输入小数点和字母 by john
function onlyNum(input) {
	$(input).keydown(function (event) {
		var keyCode = event.which;
		if (keyCode == 46 || keyCode == 8 || keyCode == 37 || keyCode == 39 || (keyCode >= 48 && keyCode <= 57) || (keyCode >= 96 && keyCode <= 105)){ 
			return true; 
		}else { return false }
	}).focus(function (){ 
		this.style.imeMode = 'disabled'; 
	});
}