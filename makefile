pix-schedule: 
	docker exec pix-manager-app-1 php bin/hyperf.php pix-schedule

notification: 
	docker exec pix-manager-app-1 php bin/hyperf.php notification