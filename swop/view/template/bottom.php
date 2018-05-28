
		<div class="template_bottom" style="margin-top:10px;">
			
			<div style="position:relative; font-size:9pt;">
				<div class="full_div" style="position:absolute;">
					<div style="height:250px; border-top:#eb454d solid 3px;"></div>
					<div style="height:51px; background-color:#ebebeb; border-top:#cccccc solid 1px; padding: 4px 0 4px 0;"></div>
				</div>
				<div style="position:relative; height:212px; line-height:20px; border-top:#eb454d solid 3px; font-size:8pt; padding-top:38px; ">
					<div class='quick_bottom' style="position:absolute; left:0px; top:38px; width:756px; height:212px; overflow:hidden; ">
						<?php
						require_once "swop/library/quick.php";
						$quick_b = new Library_Quick();
						$quick_b->navigation_bottom($this->base['url'], $this->base['tpl']);
						echo $quick_b->content['bottom'].'<div style="clear:both"></div>';
						?>
					</div>
						
					<div style="position:absolute; right:0px; top:38px; line-height:15px;">
						<div style="float:left; width:146px; height:68px; border:#e61726 1px solid; margin-right:12px;">
							<div style="float:left; width:40px; height:68px; padding:0 7px 0 13px; background: url(<?php echo $this->base['tpl']; ?>service_icon5.png) no-repeat center;"></div>
							<div style="float:left; margin-top:18px; font-weight:bold;">100%保證<br />原裝進口正品</div>
							<div style="clear:both"></div>
						</div>
						<div style="float:left; width:146px; height:68px; border:#e61726 1px solid; margin-right:12px;">
							<div style="float:left; width:40px; height:68px; padding:0 7px 0 13px; background: url(<?php echo $this->base['tpl']; ?>service_icon6.png) no-repeat center;"></div>
							<div style="float:left; margin-top:18px; font-weight:bold;">100%<br />跨境安全直送</div>
							<div style="clear:both"></div>
						</div>
						<div style="float:left; width:146px; height:68px; border:#e61726 1px solid;">
							<div style="float:left; width:40px; height:68px; padding:0 7px 0 13px; background: url(<?php echo $this->base['tpl']; ?>service_icon7.png) no-repeat center;"></div>
							<div style="float:left; margin-top:18px; font-weight:bold;">中國郵政<br />EMS</div>
							<div style="clear:both"></div>
						</div>
						<div style="clear:both"></div>
					</div>

				</div>
				<div style="position:relative; text-align:center; height:51px; line-height:25px; background-color:#ebebeb; border-top:#cccccc solid 1px; padding: 4px 0 4px 0;">
					<div class='quick_bottom_second'><?php echo $quick_b->content['bottom_second']; ?></div>
					<div id="execution_time">Copyright &copy; 2014 Crazy2go<?php echo $lang['copyright']; ?></div>
				</div>
			</div>
			
		</div>
		
	</div>
</div>
</body>
</html>