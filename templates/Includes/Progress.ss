<div>
    <table class="table">
        <tr>
            <th><%t Urban.NAME 'Name' %></th>
            <th><%t Urban.START_DATE 'Start Date' %></th>
            <th><%t Urban.END_DATE 'End Date' %></th>
        </tr>
        <% loop HistoricalNames %>
        <tr>
            <td>$Name</td>
            <td><% if StartDate %>$StartDate<% else %>--<% end_if %></td>
            <td><% if EndDate %>$EndDate<% else %>--<% end_if %></td>
        </tr>
        <% end_loop %>
    </table>
</div>
