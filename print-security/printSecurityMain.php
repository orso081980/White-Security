
<div class="wrap">
	<h2>Print Security</h2>
	<table class="table">
		<thead>
			<tr>
				<th>Username</th>
				<th>User Id</th>
				<th>Ip address</th>
				<th>Login time</th>
				<th>User Role</th>
				<th>Log in counter</th>
			</tr>
		</thead>
		<tbody>
			
			<?php foreach ($rows as $values): ?>
				<tr>
					<?php foreach ($values as $value): ?>
						<td><?= $value; ?></td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
			
		</tbody>
	</table>
</div>
