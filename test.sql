SELECT 
   *
FROM 
    users u
INNER JOIN (
    SELECT 
        *
    FROM 
        messages
    WHERE
        outgoing_msg_id = '1476082739'

        GROUP BY incoming_msg_id
            ORDER BY
        created_at DESC
) m ON u.unique_id = m.incoming_msg_id;


SELECT m.*
FROM messages m
INNER JOIN (
    SELECT incoming_msg_id, MAX(created_at) AS max_created_at
    FROM messages
    WHERE outgoing_msg_id = '1476082739'
    GROUP BY incoming_msg_id
) max_dates ON m.incoming_msg_id = max_dates.incoming_msg_id AND m.created_at = max_dates.max_created_at
ORDER BY m.created_at ASC;

SELECT u.*, m.*
FROM users u
LEFT JOIN (
    SELECT m.*
    FROM messages m
    INNER JOIN (
        SELECT incoming_msg_id, MAX(created_at) AS max_created_at
        FROM messages
        WHERE outgoing_msg_id = '1476082739'
        GROUP BY incoming_msg_id
    ) max_dates ON m.incoming_msg_id = max_dates.incoming_msg_id AND m.created_at = max_dates.max_created_at
) m ON u.unique_id = m.incoming_msg_id;


SELECT u.*, m.* FROM users u INNER JOIN ( SELECT m.* FROM messages m INNER JOIN ( SELECT incoming_msg_id, MAX(created_at) AS max_created_at FROM messages WHERE outgoing_msg_id = 757733187 GROUP BY incoming_msg_id ) max_dates ON m.incoming_msg_id = max_dates.incoming_msg_id AND m.created_at = max_dates.max_created_at ) m ON u.unique_id = m.incoming_msg_id WHERE admin = 0 AND (fname LIKE '%%' OR lname LIKE '%%') ORDER BY created_at desc