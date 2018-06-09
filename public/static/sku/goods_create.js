//window.onload = function() {
//
//	var goodsid = $("#goodsId").val();
//	var shop_type = $("#shop_type").val();
//	
//	if (goodsid == 0){
//		$("#lastPage").attr("disabled", false);
//	}else{
//		$("#lastPage").attr("disabled", true);
//		$("#lastPage").attr("href", "javascript:;");
//	}
//
//	/**
//	* 通过商品ID来查询这个商品的所有信息
//	*/
//	if (goodsid != 0) {
//		$.ajax({
//			url : "GoodsSelect",
//			type : "get",
//			data : {
//				"goodsId" : goodsid
//			},
//			success : function(res) {
////				document.write(JSON.stringify(res));
//				var goods = res;
//				/**
//				* 商品信息表
//				*/
////				$("#txtProductTitle").val(goods["goods_name"]);
////				$("#txtIntroduction").val(goods["introduction"]);
////				$("#txtKeyWords").val(goods["keywords"]);
////				$("#txtProductCostPrice").val(goods["cost_price"]);
////				$("#txtProductSalePrice").val(goods["price"]);
////				$("#txtProductMarketPrice").val(goods["market_price"]);
////				$("#txtProductCodeA").val(goods["code"]);
////				$("#txtProductCount").val(goods["stock"]);
////				$("#txtMinStockLaram").val(goods["min_stock_alarm"]);
////				$("#hidthumbnail").val(goods["thumbnail"]);
////				$("#txtProductMinPrice").val(goods["sup_min_price"]);
////				$("#goodsType").val(goods['goods_attribute_id'])
////				$("#libiary_goodsid").val(goods["library_goodsid"]);
////				$("#PurchaseSum").val(goods["max_buy"]);
//////				$("#hidQRcode").val(goods["QRcode"]);
////				$("#integration_available_use").val(goods["point_exchange"]);
////				$("#integration_available_give").val(goods["give_point"]);
////				$("input[name='stock']").each(function() {
////					$this = $(this);
////					if (goods["display_stock"] == $this.val()) {
////						this.checked = true;
////					} else {
////						this.checked = false;
////					}
////				});
////				$("input[name='shelves']").each(function() {
////					$this = $(this);
////					if (goods["state"] == $this.val()) {
////						this.checked = true;
////					} else {
////						this.checked = false;
////					}
////				});
////				
////				// 商品品牌
////				$("#brandSelect").find("option[value='" + goods['brand_id'] + "']").attr("selected", true);
////					
////				//商品所在地，区域
////				$("#provinceSelect").find("option[value='"+goods["province_id"]+"']").attr("selected",true);
////				getProvince("#provinceSelect",'#citySelect',goods["city_id"]);
////					// 运费模板选择
////				$("input[name='fare'][value='" + parseInt(goods["shipping_fee"]) + "']").attr("checked", "checked");
////				if (parseInt(goods["shipping_fee"]) == 1) {
////					$("#deliveryDiv").show();
////					$("#deliverySelect").find("option[value='" + goods['shipping_fee_id'] + "']").attr("selected", true);
////				}
////				
////				// 积分设置选择 //point_exchange_type
////				$("#integralSelect").find("option[value='" + goods["point_exchange_type"] + "']").attr("selected", true);
////				
////				//商品附加表
////				$("#BasicSales").val(goods["sales"]);
////				$("#BasicPraise").val(goods["clicks"]);
////				$("#BasicShare").val(goods["shares"]);
////
////				/**
////				 * 商品类目表
////				 */
////				$("#tbcName").attr("cname", goods["category_name"]);
////				$("#tbcName").attr("cid", goods["category_id"]);
////				$("#tbcName").html(goods['category_name']);
////				
////				/**
////				 * 商品分类表
////				 */
////				var groupIdArray = goods['goods_group_list'];
////				var $selDiv = $("#productcategory-selected");
////				var $lis = $("#procategory li");
////				var html = "";
////				for (var $i = 0; $i < groupIdArray.length; $i++) {
////					var groupId = groupIdArray[$i]["group_id"];
////					var name = groupIdArray[$i]["group_name"];
////					html += "<span class='label' id='" + groupId + "'>" + name + "<i class='categoryclose'></i></span>";
////					for (var $y = 0; $y < $lis.length; $y++) {
////						var $li = $lis[$y];
////						var $id = $($li).find("input").attr("id");
////							if (groupId == $id) {
////								$($li).find("input").attr("checked","checked");
////								break;
////							}
////						}
////					}
////					$selDiv.append(html);
////					
////					/**
////					 * 图片表
////					 */
////					var imageList = goods['img_list'];
////					
////					// 页面不显示库存 0 不显示 ，1显示
////					$('.controls input[name="stock"]').each(function() {
////						if (goods['is_stock_visible'] == 1) {
////							$('.controls input[name="stock"][value="1"]').attr("checked", true);
////						} else {
////							$('.controls input[name="stock"][value="0"]').attr("checked", true);
////						}
////					});
////					
////					var $divs = $(".previewContainer");
////					for (var $i = 0; $i < imageList.length; $i++) {
////						var $imges = imageList[$i];
////						var order = 0;// $imges["orders"];
////						var path = root + "/" + $imges["pic_cover"];
////						var imageId = $imges["pic_id"];
////						$("#file_upload_img_" + ($i + 1)).attr("src", path);
////						$("#file_upload_img_" + ($i + 1)).attr("data-id",imageId);
////						img_id_arr += imageId + ",";
////					}
////					img_id_arr = img_id_arr.substr(0, img_id_arr.length - 1);
////					
////					if(goods['goods_attribute_id'] > 0){
////						//加载SKU
//////						getGoodsSpecListByAttrId($("#goodsType").val());
////					}
////
//////					editSkuData(goods['goods_spec_format'],goods['sku_list']);
////					//加载属性
////					$(".js-goods-sku-attribute tr").each(function(){
////						var value = $(this).children("td:first").attr("data-value");//商品属性名称
////						var value_name = $(this).children("td:last");//具体的属性值
////						if(value != undefined && value != ""){
////							for(var i=0;i<goods['goods_attribute_list'].length;i++){
////								var curr = goods['goods_attribute_list'][i];
////								if(curr['attr_value'] == value){
////									switch(value_name.find("input").attr("type")){
////										case "text":
////											value_name.find("input").val(curr['attr_value_name']);
////											break;
////										case "radio":
////											value_name.find("input").each(function(){
////												if($.trim($(this).val()) == $.trim(curr['attr_value_name'])){
////													$(this).attr("checked","checked");
////													return false;
////												}
////											})
////											break;
////										case "checkbox":
////											value_name.find("input").each(function(){
////												if($.trim($(this).val()) == $.trim(curr['attr_value_name'])){
////													$(this).attr("checked","checked");
////												}
////											})
////											break;
////									}
////									break;
////								}
////							}
////						}
////					});
////					
////					
////					ue.ready(function() {
////						ue.setContent(goods["description"], false);
////					});
//					
//				}
//		});
//	}
//	
//};