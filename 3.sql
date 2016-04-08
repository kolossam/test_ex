
-- Table structure
CREATE TABLE `data` (
  `id` SERIAL,
  `type` VARCHAR(6) NOT NULL,
  `date` DATE NOT NULL,
  `value` INTEGER UNSIGNED NOT NULL
);

-- Table sample data
INSERT INTO `data` VALUES
  (null, 'photo', '2015-02-02', 1240),
  (null, 'image', '2015-02-02', 5609),
  (null, 'photo', '2015-02-01', 1190),
  (null, 'review', '2015-02-02', 3600);



--- postgresql

WITH tmp as (SELECT "type", max("date") "date"
FROM "data" 
GROUP BY "type") 
SELECT DISTINCT ON("d"."type", "d"."date") "d"."type", "d"."date", "d"."value"
FROM tmp 
LEFT JOIN "data" d
ON ("d"."type" = tmp."type" AND "d"."date" = tmp."date")
ORDER BY "d"."type", "d"."date", "d"."value" DESC
