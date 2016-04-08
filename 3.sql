--- postgresql

WITH tmp as (SELECT "type", max("date") "date"
FROM "data" 
GROUP BY "type") 
SELECT DISTINCT ON("d"."type", "d"."date") "d"."type", "d"."date", "d"."value"
FROM tmp 
LEFT JOIN "data" d
ON ("d"."type" = tmp."type" AND "d"."date" = tmp."date")
ORDER BY "d"."type", "d"."date", "d"."value" DESC
