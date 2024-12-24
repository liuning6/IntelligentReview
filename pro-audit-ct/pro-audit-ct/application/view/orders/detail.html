{include file="common/header"}
<style>
#addr{color:#fff;font-weight:bold;display:inline-block;font-size:16px;}
.imgs .show-image{margin:10px; width: 320px; height: 420px;} .img-text p{text-align:center;font-size:18px;font-weight:bold;}.img-text{width:320px;padding: 10px 0 7px;}.img-text a{color:#fff;}
.img-text.s1{background-color: rgba(84, 216, 51, 0.8);}
.img-text.s2{background-color: rgba(237, 50, 7, 0.8);}
</style>
        <div class="contentBox">
			<div class="list-tool">
				<button class="search" onclick="location.href='{$Think.session.orderindex}'" style="width:80px;margin:0 20px;"><<工单列表</button>
				<span id="addr" title="{$order.addr}">{:mb_cut($order.addr, 30)} ({$order.gid})</span>
				<button class="search" onclick="window.location.href='{:U('/orders/detail', ['id'=>$id])}';">全部</button>
				<form action="{:U('/orders/detail', ['id'=>$id])}" style="display:inline;">
					<select name="status" style="width:80px;" id="status">
						<option value="">状态</option>
						<option value="0">待审核</option>
						<option value="1">合格</option>
						<option value="2">不合格</option>
					</select>
					<select name="type" style="width:90px;" id="type"><option value="">图片分类</option></select>

					<button class="search" type="submit">搜索</button>
				</form>
				<button class="refresh" onclick="window.location.reload();">刷新</button>
            </div>
			
			
            <div class="tab-space">
                <table class="list-tab imgShow">
                    <tbody>
                        <tr>
                            <div class="imgs" style="margin-bottom:20px;">
							{foreach name="data" item="val"}
							    
								<div class="show-image">
									<a target="_blank" href="/source/pics/{:date('Ym', strtotime($val.etime))}/{$val.oid}/{$val.filename}.{$val.extension}"><img src="/source/pics/{:date('Ym', strtotime($val.etime))}/{$val.oid}/{$val.filename}.{$val.extension}" alt="{$val.status ? $fs3[$val.code] : '待审核'}" title="{$val.id} {$val.matching} {$val.ycode} {$val.code}" /></a>
									<div class="img-text s{$val.status}">
										{if $val.ycode == 45} 
                                                                                    {if $val.username}
                                                                                        {if $val.cause}
                                                                                        <p style="font-size: 10px;">{$val.usercode . '-' . $val.username . '-' . $val.userrole. '-' . $val.cause . ' <a target="_blank" code="'. $val.code .'">&nbsp;</a>'}</p>
                                                                                        {else}
                                                                                        <p style="font-size: 14px;">{$val.usercode . '-' . $val.username . '-' . $val.userrole . ' <a target="_blank" code="'. $val.code .'">&nbsp;</a>'}</p>
                                                                                        {/if}
                                                                                    {else}
										    <p style="font-size: 14px;">{$ycodes[$val.ycode] . '-' . $val.cause . ' <a target="_blank" code="'. $val.code .'">&nbsp;</a>'}</p>
                                                                                    {/if}
										{else}
										<p>{$val.status ? $ycodes[$val.ycode] . ' <a target="_blank" code="'. $val.code .'">&nbsp;</a>' : '待审核'}</p>
										{/if}
									</div>
								</div>
							{/foreach}
							</div>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
<script>
var fs2 = {$fs2 ? json_encode($fs2, 320) : '[]'};
var fs3 = {$fs3 ? json_encode($fs3, 320) : '[]'};
console.log(fs2);
console.log(fs3);
$(function(){
	
	var h = '';
	$.each(fs2, function(i, v){
		h += '<option value="'+ i +'">'+ v +'</option>';
	});
	h += '<option value="99">供水服务代表</option>';
	h += '<option value="98">固定采样箱内部</option>';
	h += '<option value="0">其它</option>';
	$('#type').append(h);
	$('#type').val('{:I('type')}');
	$('#status').val('{:I('status')}');
{if condition="$Think.session.user.uid lt 3"}	$('a[code]').each(function(){
		code = parseInt($(this).attr('code'));
		$(this).attr('href', '{:U('/tools/mt');}?type='+ ($.inArray(code, [4000, 4101, 4201, 4301]) > -1 ? 2 : 1) +'&file=' + $(this).parents('.show-image').find('img').attr('src'));
	});
{/if}
});
</script>
{include file="common/footer"}
