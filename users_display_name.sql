select
	wp_users.ID,
	wp_usermeta.meta_key,
	wp_usermeta.meta_value
from
	wp_users,
	wp_usermeta
where
	wp_users.ID = wp_usermeta.user_id AND
	( wp_usermeta.meta_key = 'first_name' OR wp_usermeta.meta_key = 'last_name' ) AND
	NULLIF( wp_usermeta.meta_value , '') IS NOT NULL
-- group by
--	wp_usermeta.umeta_id
-- 	wp_usermeta.meta_key
--	wp_users.ID
;
