<% if $Images %>
    <div class="image_carousel">				
	<div id="carousel">
	      <% loop Images.Sort(Title) %>
		  <img src="$URL" <% if $Top.GenItemWidth > 0 %>width="$Top.GenItemWidth"<% end_if %> <% if $Top.GenItemHeight > 0 %>height="$Top.GenItemHeight"<% end_if %>/>		  
	      <% end_loop %>
	</div>
	
	<div class="clearfix"></div>
	<a class="prev" id="prev_img" href="#"><span>prev</span></a>
	<a class="next" id="next_img" href="#"><span>next</span></a>
	<div class="pagination" id="pager_icon"></div>	
  </div>
<% end_if %>