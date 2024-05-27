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

SELECT *
FROM user_topics ut
JOIN topics t ON ut.topic_id = t.id
WHERE ut.user_id = 3

SELECT
    t.*,
    ut.user_id
FROM
    topic t
LEFT JOIN user_topics ut ON
    t.id = ut.topic_id AND ut.user_id = :user_id


    
    SELECT 
        ut.*,u.fname,u.lname,u.img
    FROM user_topics ut
    JOIN  users u ON
        ut.user_id = u.user_id AND ut.topic_id = 13
// contact topics
SELECT t.*
FROM topic t
LEFT JOIN (
	SELECT m.*msg_id
    FROM messages
INNER JOIN (
        SELECT incoming_msg_id, MAX(created_at) AS max_created_at
        FROM messages
        WHERE outgoing_msg_id = 1656696425 AND m.topic_id NOT null
        GROUP BY incoming_msg_id
    ) max_dates ON m.incoming_msg_id = max_dates.incoming_msg_id AND m.created_at = max_dates.max_created_at
    ) m ON t.id= m.topic_id



SELECT t.*, m.msg, u.fname, u.lname, u.img
                FROM topic t
                JOIN (
                    SELECT m.*
                    FROM messages m
                    INNER JOIN (
                        SELECT incoming_msg_id, MAX(created_at) AS max_created_at
                        FROM messages
                        WHERE outgoing_msg_id = 757733187 AND topic_id IS NOT NULL
                        GROUP BY incoming_msg_id
                    ) max_dates ON m.incoming_msg_id = max_dates.incoming_msg_id AND m.created_at = max_dates.max_created_at
                ) m ON t.id = m.topic_id
                JOIN users u ON m.incoming_msg_id = u.unique_id