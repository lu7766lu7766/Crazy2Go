
		<div class="template_center">		
			<div style="text-align:center; line-height:30px;">
				<form id="verification_email">
					<div><label for="email">信箱：</label><input type="text" name="email" id="email" style="width:120px;" /></div>
					<div><label for="verification_key">身份驗證碼：</label><input type="text" name="verification_key" id="verification_key" value="<?php echo $_GET['key']; ?>" /></div>
					<div><input type="submit" value="驗證" style="width:160px; height:35; border:1px solid grey; line-height:normal; *overflow:visible;" /></div>
				</form>
			</div>
		</div>
