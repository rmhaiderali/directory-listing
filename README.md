# PHP Directory Listing Script
This is a compact and portable script designed to effortlessly showcase files and folders in a clean, mobile-friendly manner. Simply place the `index.php` file in your desired folder, and you are good to go.

# Parameters

You can provide parameters as query parameters. For instance, use the following format: `folder/?dark=1&media=1&path=/directory`.

<table style="table-layout:fixed; white-space: nowrap">

<tr>
<th>Parameter</th>
<th>Default Value</th>
<th>Possible Values</th>
<th>Usage Note</th>
</tr>

<tr>
<td>path</td>
<td>dir where file is located</td>
<td>desired dir path</td>
<td>allways start with /</td>
</tr>

<tr>
<td>media</td>
<td>0</td>
<td>0 or 1</td>
<td>0: no media
<br>1: show media</td>
</tr>

<tr>
<td>dark</td>
<td>1</td>
<td>0 or 1</td>
<td>0: light theme
<br>1: dark theme</td>
</tr>

</table>