<tr>
	<td class="row-header mod-title">$Description</td>
	<td class="totals-column" colspan="3">$Price.Nice</td>
</tr>
<% if StoreTitle %>
	<tr>
		<td class="row-header mod-title"></td>
		<td class="totals-column" colspan="3">
			<strong>$StoreTitle</strong><br />
			
			<% if StoreAddress %>
				$StoreAddress <br />
			<% end_if %>
			
			<% if StorePhone %>
				Phone: $StorePhone
			<% end_if %>
		</td>
	</tr>
<% end_if %>