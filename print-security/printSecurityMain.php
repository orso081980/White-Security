
<div class="wrap">
	<h2>Print Security</h2>
	<table class="table table-striped">
		<thead class="thead-dark">
			<tr>
				<th>Username</th>
				<th>User Id</th>
				<th>Ip address</th>
				<th>Last Login time</th>
				<th>Last Logout time</th>
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
