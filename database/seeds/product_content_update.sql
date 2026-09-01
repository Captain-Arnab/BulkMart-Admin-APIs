-- product_content_update.sql
-- Populate products.description / benefits / storage_tips for the confirmed 34-product catalog.
-- Idempotent: safe to re-run. Matches by exact products.name (live DB).
-- Generated: 2026-09-01 11:22:43

SET NAMES utf8mb4;
SET time_zone = '+00:00';

START TRANSACTION;

-- #1 Arvi (Chamagadda) → `Arvi (colocasia)` (id 79)
UPDATE products
SET description = 'Arvi, also commonly known as Chamagadda, Colocasia, or Taro Root, is a popular root vegetable with a mild earthy flavour and a soft, creamy texture after cooking. It is widely used in Indian cuisine for curries, fry preparations, gravies, stews, and traditional regional dishes. VEGGIICART supplies fresh and carefully selected Arvi suitable for household as well as bulk commercial requirements. As it is a naturally grown vegetable, its size, shape, and colour may vary.',
    benefits = 'Arvi is a natural source of carbohydrates and dietary fibre and contains various naturally occurring vitamins and minerals. The carbohydrate content makes it a useful source of energy, while dietary fibre can contribute to normal digestive health when included as part of a balanced diet. It is filling, versatile, and suitable for several traditional recipes.',
    storage_tips = 'Store unwashed Arvi in a cool, dry, dark, and well-ventilated place away from direct sunlight and excess moisture. Avoid washing before storage, as additional moisture can encourage spoilage. Refrigerate if longer storage is required. Arvi should always be properly cooked before consumption.'
WHERE name = 'Arvi (colocasia)';

-- #2 Beans → `Beans` (id 61)
UPDATE products
SET description = 'Fresh Beans are tender green vegetables commonly used in Indian and international cuisine. Their mild flavour and slightly crisp texture make them suitable for curries, stir-fries, pulao, fried rice, salads, mixed vegetable dishes, and continental recipes. VEGGIICART supplies fresh Beans selected for quality and freshness, making them suitable for restaurants, caterers, hostels, hotels, and other bulk kitchens.',
    benefits = 'Beans are naturally low in fat and provide dietary fibre along with vitamins and minerals. They can contribute to a balanced diet and are especially useful for adding vegetables and fibre to everyday meals. Their versatility makes them easy to combine with other vegetables, grains, and protein-rich foods.',
    storage_tips = 'Store Beans unwashed in the vegetable compartment of the refrigerator, preferably in a perforated or loosely closed food-safe bag. Keep them dry and avoid excess moisture. Wash only before cooking. For best texture and freshness, use them as soon as reasonably possible after purchase.'
WHERE name = 'Beans';

-- #3 Chikkudu (Broad / Hyacinth Beans) → `Broad Beans (chikkudu)` (id 62)
UPDATE products
SET description = 'Chikkudu, commonly known as Hyacinth Beans or Broad Beans in regional usage, is a traditional vegetable widely used in South Indian cooking. The pods have a distinctive flavour and are commonly prepared in curries, stir-fries, dals, and mixed vegetable preparations. Fresh Chikkudu provides a hearty texture and works particularly well with spices, coconut, lentils, and other vegetables.',
    benefits = 'Chikkudu naturally provides dietary fibre, plant-based nutrients, and carbohydrates. It can help increase the vegetable and fibre content of meals and forms a nutritious part of a varied diet. The beans and tender pods are widely valued in traditional cooking for their flavour and satisfying texture.',
    storage_tips = 'Keep fresh Chikkudu in the refrigerator vegetable compartment in a breathable bag or container. Do not wash before storage unless the beans will be used immediately. Remove damaged or excessively soft pods before storing to help maintain the quality of the remaining produce.'
WHERE name = 'Broad Beans (chikkudu)';

-- #4 Beetroot → `Beetroot` (id 80)
UPDATE products
SET description = 'Beetroot is a colourful root vegetable known for its characteristic deep red-purple colour, mild earthy flavour, and natural sweetness. It can be used raw, boiled, steamed, roasted, grated into salads, blended into juices, or incorporated into curries and other dishes. Its vibrant colour also makes it popular in restaurants, catering services, juice centres, and institutional kitchens.',
    benefits = 'Beetroot naturally contains dietary fibre, folate, potassium, and naturally occurring plant compounds, including pigments called betalains. It can contribute to a nutrient-rich balanced diet and works well in salads, juices, and cooked preparations.',
    storage_tips = 'Remove leafy tops, if attached, before storing the roots. Keep Beetroot unwashed in the refrigerator vegetable drawer, preferably in a perforated bag. Avoid cutting until required, as whole beetroot generally retains freshness better. Cut beetroot should be refrigerated in a covered food-safe container.'
WHERE name = 'Beetroot';

-- #5 Bitter Gourd (Karela) → `Bittergourd` (id 63)
UPDATE products
SET description = 'Bitter Gourd, commonly called Karela, is a distinctive green vegetable known for its naturally bitter taste. It is an important ingredient in many Indian cuisines and is commonly prepared as stir-fry, stuffed karela, curry, chips, or combined with onions and spices. Its characteristic flavour makes it particularly popular in traditional home-style and regional dishes.',
    benefits = 'Bitter Gourd provides dietary fibre and naturally occurring vitamins and minerals. It is relatively low in calories and can be included as part of a varied vegetable-rich diet. Because of its strong natural flavour, it is frequently prepared with complementary spices and ingredients.',
    storage_tips = 'Store Bitter Gourd in the refrigerator vegetable compartment in a breathable bag. Keep it dry and separate from produce that releases excessive moisture. Do not wash until shortly before preparation. Use while the gourds remain firm and fresh.'
WHERE name = 'Bittergourd';

-- #6 Bottle Gourd (Lauki / Sorakaya) → `Bottle gourd` (id 64)
UPDATE products
SET description = 'Bottle Gourd, also known as Lauki, Sorakaya, or Dudhi, is a mild-flavoured vegetable with soft flesh and high natural moisture content. It is widely used for curries, dals, soups, kofta, stews, and traditional sweets. Because of its neutral taste, Bottle Gourd readily absorbs the flavours of spices and other ingredients.',
    benefits = 'Bottle Gourd has a high natural water content and provides dietary fibre along with various naturally occurring nutrients. It is generally light and versatile and can form part of balanced everyday meals.',
    storage_tips = 'Store whole Bottle Gourd in a cool place for short periods or refrigerate for longer freshness. Once cut, wrap the exposed portion or place it in an airtight food-safe container and refrigerate. Use cut Bottle Gourd promptly for best quality.'
WHERE name = 'Bottle gourd';

-- #7 Brinjal – Black → `Brinjal (black)` (id 65)
UPDATE products
SET description = 'Black Brinjal is a glossy, dark-coloured variety of eggplant known for its tender flesh and ability to absorb flavours during cooking. Depending on size, it is suitable for curries, stuffed dishes, roasting, grilling, frying, bharta, and gravies. It is an important everyday vegetable in Indian commercial and household kitchens.',
    benefits = 'Brinjal naturally contains dietary fibre and a variety of plant nutrients while being relatively low in calories. Its fibre content can contribute to a balanced diet, while its soft texture makes it suitable for numerous cooked preparations.',
    storage_tips = 'Store Brinjal in a cool place or refrigerator vegetable compartment, depending on how soon it will be used. Avoid exposing it to excessive cold for prolonged periods. Keep it dry and handle carefully to prevent bruising.'
WHERE name = 'Brinjal (black)';

-- #8 Brinjal – White → `Brinjal (white)` (id 66)
UPDATE products
SET description = 'White Brinjal is an attractive eggplant variety distinguished by its pale white or creamy skin and soft flesh. It has a mild flavour and is suitable for curries, gravies, stuffed preparations, frying, roasting, and traditional regional recipes. Its distinctive appearance can also add visual variety to mixed vegetable preparations.',
    benefits = 'White Brinjal naturally provides dietary fibre and plant-based nutrients. It can contribute to vegetable intake as part of a balanced diet and is a versatile alternative to darker varieties of eggplant.',
    storage_tips = 'Keep White Brinjal dry and refrigerated in the vegetable drawer if it is not being used immediately. Avoid washing before storage and protect it from pressure or bruising. Once cut, refrigerate in a covered container and consume promptly.'
WHERE name = 'Brinjal (white)';

-- #9 Brinjal Long – Purple → `Brinjal long (purple)` (id 67)
UPDATE products
SET description = 'Long Purple Brinjal is a slender eggplant variety with glossy purple skin and soft, tender flesh. Its elongated shape makes it particularly convenient for slicing and is commonly used for stir-fries, curries, fried preparations, gravies, grilling, and mixed vegetable dishes.',
    benefits = 'Long Purple Brinjal provides dietary fibre and naturally occurring plant compounds. It is low in fat and can be incorporated easily into a wide variety of vegetable-based dishes.',
    storage_tips = 'Store in the refrigerator vegetable compartment and avoid keeping it wet. Handle gently, as the skin can bruise under pressure. For best quality, cut only when ready to cook.'
WHERE name = 'Brinjal long (purple)';

-- #10 Cabbage – Big Size → `Cabbage (big size)` (id 68)
UPDATE products
SET description = 'Big Size Cabbage consists of tightly packed, crisp leaves with a mild and slightly sweet flavour. Larger heads are especially useful for restaurants, caterers, hotels, canteens, hostels, and other high-volume kitchens. It is suitable for curries, stir-fries, salads, noodles, fried rice, soups, rolls, and numerous Indo-Chinese dishes.',
    benefits = 'Cabbage provides dietary fibre and naturally occurring vitamins such as vitamin C and vitamin K. It is low in calories and can be used to increase the vegetable content of meals.',
    storage_tips = 'Store whole Cabbage in the refrigerator vegetable drawer. Avoid washing before storage. Once cut, tightly wrap the remaining portion or store in a sealed food-safe container and refrigerate.'
WHERE name = 'Cabbage (big size)';

-- #11 Carrot → `Carrot` (id 81)
UPDATE products
SET description = 'Carrot is a crisp root vegetable recognised for its bright colour, mildly sweet flavour, and wide culinary use. It can be eaten raw, steamed, boiled, roasted, sautéed, juiced, or added to curries, salads, soups, pulao, fried rice, desserts, and mixed vegetable dishes.',
    benefits = 'Carrots are well known as a natural source of beta-carotene, which the body can convert into vitamin A. They also provide dietary fibre and other naturally occurring nutrients. Vitamin A contributes to normal vision and immune function when consumed as part of an overall balanced diet.',
    storage_tips = 'Store Carrots in the refrigerator vegetable compartment, preferably with leafy tops removed. Keep them dry and in a food-safe bag or container. Wash and peel as required immediately before use.'
WHERE name = 'Carrot';

-- #12 Cluster Beans (Goruchikkudu / Guar) → `Cluster beans` (id 70)
UPDATE products
SET description = 'Cluster Beans are slender green pods with a characteristic slightly earthy taste. Known regionally as Goruchikkudu or Guar, they are frequently cooked with onions, lentils, coconut, spices, or other vegetables. They are popular in traditional Indian curries and stir-fried preparations.',
    benefits = 'Cluster Beans naturally contain dietary fibre along with vitamins and minerals. Their fibre content makes them a useful addition to balanced vegetable-based meals.',
    storage_tips = 'Keep Cluster Beans refrigerated, dry, and unwashed in a breathable bag or vegetable container. Trim the ends and wash only before cooking. Remove any spoiled pods before storage.'
WHERE name = 'Cluster beans';

-- #13 Coriander Leaves → `Coriander leaves` (id 93)
UPDATE products
SET description = 'Fresh Coriander Leaves, commonly called Dhania or Kothimeera, are aromatic herbs extensively used in Indian cooking. They are used for garnishing curries, biryanis, soups, chaats, chutneys, salads, marinades, and numerous regional dishes. Their distinctive aroma instantly adds freshness and flavour to food.',
    benefits = 'Coriander leaves naturally contain vitamins, minerals, and plant-based compounds while adding flavour to dishes without requiring large quantities of salt or heavy seasonings.',
    storage_tips = 'Remove damaged leaves and store Coriander in the refrigerator. It may be wrapped loosely in slightly damp kitchen paper and placed in a container, or the stems may be kept in a small quantity of clean water. Avoid excess moisture on the leaves.'
WHERE name = 'Coriander leaves';

-- #14 English Cucumber (Black) → `Cucumber (English) black` (id 71)
UPDATE products
SET description = 'English Cucumber is valued for its refreshing taste, crisp texture, and high natural moisture content. It is commonly used in salads, sandwiches, raita, cold platters, juices, infused water, and continental preparations. It is particularly suitable for hotels, restaurants, salad counters, and catering businesses.',
    benefits = 'Cucumber naturally has a high water content and provides small amounts of fibre, vitamins, and minerals. It can be a refreshing addition to salads and balanced meals.',
    storage_tips = 'Store Cucumbers in the refrigerator vegetable drawer, preferably dry and loosely wrapped. Avoid storing cut pieces uncovered. Refrigerate cut cucumber in an airtight food-safe container and use promptly.'
WHERE name = 'Cucumber (English) black';

-- #15 Yellow Round Cucumber → `Cucumber yellow (round)` (id 72)
UPDATE products
SET description = 'Yellow Round Cucumber is a traditional cucumber variety recognised by its rounded shape and yellow-green colour as it matures. It has a fresh, mildly tangy flavour and is commonly used in salads, chutneys, dals, curries, and regional South Indian recipes.',
    benefits = 'It contains a high proportion of natural moisture along with dietary fibre and naturally occurring micronutrients. It makes a refreshing addition to meals and can be served both raw and cooked depending on the variety and recipe.',
    storage_tips = 'Keep Yellow Cucumbers in a cool place for immediate use or refrigerate to maintain freshness. Once cut, place them in a covered container and refrigerate. Avoid prolonged exposure to moisture.'
WHERE name = 'Cucumber yellow (round)';

-- #16 Drumsticks (Moringa Pods) → `Drumsticks` (id 73)
UPDATE products
SET description = 'Drumsticks are the long green pods of the Moringa tree and are an important ingredient in South Indian cuisine. Their soft inner pulp develops a distinctive flavour when cooked and is commonly used in sambar, dal, vegetable curries, soups, and gravies.',
    benefits = 'Drumstick pods naturally provide dietary fibre and a variety of vitamins and minerals. They are a traditional vegetable that can add both flavour and nutritional variety to regular meals.',
    storage_tips = 'Keep Drumsticks in the refrigerator, wrapped loosely or placed in a breathable food-safe bag. Longer pods may be cut into manageable lengths before refrigeration. Avoid keeping cut pieces exposed.'
WHERE name = 'Drumsticks';

-- #17 Garlic → `Garlic` (id 82)
UPDATE products
SET description = 'Garlic is an essential culinary ingredient with a strong aroma and distinctive savoury flavour. It is used in Indian, Asian, continental, and international cuisines for curries, sauces, chutneys, marinades, soups, stir-fries, pickles, and seasoning mixtures.',
    benefits = 'Garlic contains naturally occurring sulphur-containing compounds and other plant nutrients. Although generally consumed in relatively small quantities, it is a useful flavouring ingredient and can help add depth to dishes.',
    storage_tips = 'Store whole Garlic bulbs in a cool, dry, dark, and well-ventilated place. Avoid sealed plastic bags and moisture. Peeled cloves should be kept refrigerated in a clean, airtight food-safe container and used promptly.'
WHERE name = 'Garlic';

-- #18 Ginger → `Ginger` (id 83)
UPDATE products
SET description = 'Fresh Ginger is an aromatic root widely used for its warm, spicy flavour. It is an essential ingredient in curries, gravies, tea, soups, marinades, chutneys, stir-fries, and ginger-garlic pastes. It is widely required by commercial kitchens in regular quantities.',
    benefits = 'Ginger contains naturally occurring plant compounds, including gingerols, and is commonly used as part of a varied diet. Its strong natural flavour allows a relatively small quantity to enhance an entire dish.',
    storage_tips = 'Store fresh Ginger unwashed in the refrigerator for extended freshness. Keep it dry and preferably in an airtight or loosely ventilated food-safe container. Peel or cut only the amount required for immediate use.'
WHERE name = 'Ginger';

-- #19 Ivy Gourd (Dondakaya / Tindora) → `Ivy gourd` (id 74)
UPDATE products
SET description = 'Ivy Gourd, also known as Dondakaya, Tindora, or Kovakkai, is a small green vegetable with a firm, slightly crunchy texture. It is especially popular in South Indian cooking and is used for stir-fries, curries, masala preparations, and rice accompaniments.',
    benefits = 'Ivy Gourd provides dietary fibre and naturally occurring vitamins and minerals while being relatively low in calories. It is suitable for adding variety to everyday vegetable dishes.',
    storage_tips = 'Store fresh Ivy Gourd in the refrigerator vegetable compartment in a breathable food-safe bag. Keep it dry and avoid washing until cooking. Use while the gourds remain firm.'
WHERE name = 'Ivy gourd';

-- #20 Ladyfinger (Okra / Bhindi) → `Ladyfinger` (id 75)
UPDATE products
SET description = 'Ladyfinger, commonly known as Okra or Bhindi, is a tender green vegetable widely used in Indian cooking. Its mild flavour works well in dry fry preparations, curries, sambar, gravies, and stuffed dishes.',
    benefits = 'Ladyfinger naturally provides dietary fibre, folate, and other vitamins and minerals. It can contribute to a fibre-rich balanced diet and is a versatile vegetable for everyday meals.',
    storage_tips = 'Ladyfinger should be kept dry, as moisture can cause it to deteriorate quickly. Store it unwashed in the refrigerator in a breathable bag. Wash only immediately before cutting and cooking.'
WHERE name = 'Ladyfinger';

-- #21 Lemon → `Lemon` (id 88)
UPDATE products
SET description = 'Fresh Lemons are citrus fruits known for their refreshing aroma, tangy flavour, and acidic juice. They are widely used in beverages, salads, marinades, chutneys, pickles, curries, desserts, and as a finishing ingredient for many dishes.',
    benefits = 'Lemons are naturally recognised as a source of vitamin C and contain various plant compounds. Their strong flavour can enhance food and beverages even when used in relatively small quantities.',
    storage_tips = 'Lemons can be kept in a cool, dry place for short-term use, while refrigeration helps maintain freshness for longer. Cut lemons should always be covered and refrigerated.'
WHERE name = 'Lemon';

-- #22 Mint Leaves (Pudina) → `Mint leaves` (id 94)
UPDATE products
SET description = 'Mint Leaves, commonly known as Pudina, are refreshing aromatic herbs widely used in chutneys, biryanis, pulao, salads, beverages, raita, marinades, garnishes, and traditional dishes. Their cool, distinctive aroma makes them highly popular in both Indian and international cuisine.',
    benefits = 'Mint naturally contains various plant compounds and small quantities of vitamins and minerals. It adds strong flavour and freshness to food and beverages without adding significant calories.',
    storage_tips = 'Keep Mint refrigerated. Remove damaged leaves and wrap the bunch loosely in slightly damp kitchen paper or store the stems upright in a little clean water. Avoid excessive moisture and use tender leaves while fresh.'
WHERE name = 'Mint leaves';

-- #23 Onion – Big Size → `Onion big size` (id 84)
UPDATE products
SET description = 'Big Size Onions are particularly suitable for restaurants, catering operations, hotels, institutional kitchens, and other businesses requiring larger onions for efficient preparation. They offer the characteristic pungent-sweet onion flavour and are used in curries, gravies, biryanis, salads, soups, sauces, and fried preparations.',
    benefits = 'Onions provide dietary fibre and naturally occurring plant compounds, including flavonoids. They are an important everyday ingredient and help build flavour in numerous cooked and raw dishes.',
    storage_tips = 'Store whole Onions in a cool, dry, dark, and well-ventilated area. Avoid sealed plastic bags and excessive moisture. Keep them away from potatoes where possible. Refrigerate peeled or cut onions in a closed container.'
WHERE name = 'Onion big size';

-- #24 Onion – Medium Size → `Onion medium size` (id 85)
UPDATE products
SET description = 'Medium Size Onions offer convenient portioning and consistent usage for everyday cooking. They are highly versatile and suitable for curries, gravies, stir-fries, salads, biryanis, soups, sauces, and numerous Indian preparations.',
    benefits = 'Onions naturally contain fibre, vitamins, minerals, and plant compounds. They add flavour and texture to dishes and can be consumed either raw or cooked according to the recipe.',
    storage_tips = 'Keep unpeeled Onions in a cool, dry, well-ventilated place away from sunlight. Avoid storing them in damp environments. Refrigerate after peeling or cutting.'
WHERE name = 'Onion medium size';

-- #25 Onion – Small Size → `Onion small size` (id 86)
UPDATE products
SET description = 'Small Onions are convenient for preparations where whole or lightly chopped onions are preferred. Depending on variety, they can be used in sambar, curries, pickles, gravies, roasting, and traditional regional recipes.',
    benefits = 'Like other onion varieties, Small Onions provide dietary fibre and naturally occurring plant nutrients. Their concentrated flavour can add richness and aroma to cooked dishes.',
    storage_tips = 'Store in a cool, dry, ventilated area, away from moisture and direct sunlight. Discard onions that become excessively soft or show signs of spoilage. Refrigerate peeled onions.'
WHERE name = 'Onion small size';

-- #26 Potato → `Potato` (id 87)
UPDATE products
SET description = 'Potato is one of the most widely used and versatile vegetables, valued for its mild flavour and satisfying texture. It can be boiled, fried, roasted, baked, mashed, or used in curries, biryanis, snacks, cutlets, soups, and numerous Indian and international preparations.',
    benefits = 'Potatoes are primarily a source of carbohydrates and also naturally provide potassium, vitamin C, and dietary fibre, particularly when prepared appropriately. Their carbohydrate content makes them a useful source of dietary energy.',
    storage_tips = 'Keep Potatoes in a cool, dark, dry, and well-ventilated area, away from direct sunlight. Avoid refrigeration for routine storage where possible. Do not consume potatoes that have become extensively green or spoiled.'
WHERE name = 'Potato';

-- #27 Tomato → `Tomato` (id 113)
UPDATE products
SET description = 'Tomatoes are a kitchen essential recognised for their combination of mild sweetness, acidity, juiciness, and bright colour. They are extensively used in curries, gravies, salads, soups, sauces, chutneys, sambar, rasam, sandwiches, and many other preparations.',
    benefits = 'Tomatoes naturally provide vitamin C and plant compounds such as lycopene, along with other nutrients. They add flavour, colour, moisture, and nutritional variety to meals.',
    storage_tips = 'Firm Tomatoes can be stored at room temperature away from direct sunlight until ripe. Once fully ripe, refrigeration can help slow further ripening. Cut Tomatoes should always be refrigerated in a covered container.'
WHERE name = 'Tomato';

-- #28 apple → `apple` (id 89)
UPDATE products
SET description = 'Apples are popular fruits known for their crisp texture, naturally sweet-tart flavour, and convenient everyday use. They can be eaten fresh or incorporated into fruit salads, juices, desserts, breakfast preparations, bakery products, and catering menus.',
    benefits = 'Apples naturally contain dietary fibre, vitamin C, and various plant compounds. Eating whole apples with the edible skin, after thorough washing, can provide more fibre than consuming only strained juice.',
    storage_tips = 'Store Apples in a cool place for short-term use or refrigerate for longer freshness. Keep away from damaged fruit, as one spoiled apple can affect surrounding produce. Wash immediately before eating or preparation.'
WHERE name = 'apple';

-- #29 avocado → `avocado` (id 90)
UPDATE products
SET description = 'Avocado is a creamy-textured fruit with a mild, rich flavour. It is popular in salads, sandwiches, toast, smoothies, dips such as guacamole, breakfast bowls, and contemporary restaurant menus. Avocados are typically supplied firm to moderately ripe depending on availability.',
    benefits = 'Avocados are naturally rich in unsaturated fats and also provide dietary fibre, potassium, folate, and several vitamins. Their creamy texture makes them a popular ingredient in nutritious and plant-based dishes.',
    storage_tips = 'Keep firm Avocados at room temperature until they ripen. Once ripe, refrigerate to slow further ripening. Cut Avocado should be covered tightly and refrigerated; a little lemon juice may help reduce surface browning.'
WHERE name = 'avocado';

-- #30 banana → `banana` (id 91)
UPDATE products
SET description = 'Bananas are naturally sweet fruits with a soft texture and convenient edible portion. They are widely consumed directly and are also used in smoothies, shakes, fruit salads, desserts, bakery preparations, breakfast dishes, and catering menus.',
    benefits = 'Bananas naturally provide carbohydrates, potassium, vitamin B6, and dietary fibre. Their carbohydrate content makes them a convenient source of dietary energy and they are easy to include in everyday meals and snacks.',
    storage_tips = 'Store Bananas at room temperature away from direct sunlight and heat. Keep them separated from delicate fruits if rapid ripening is not desired. Very ripe bananas may be refrigerated; the peel can darken even though the fruit inside remains usable.'
WHERE name = 'banana';

-- #31 Boppaya (Papaya) → `boppaya (papaya)` (id 92)
UPDATE products
SET description = 'Boppaya, commonly known as Papaya, is a tropical fruit with soft orange flesh and a naturally sweet flavour when ripe. Ripe Papaya is eaten fresh and used in juices, fruit bowls, desserts, and smoothies, while raw Papaya can be used in certain savoury preparations.',
    benefits = 'Ripe Papaya naturally provides vitamin C, vitamin A precursors, dietary fibre, and various plant nutrients. It also naturally contains the enzyme papain. It makes a colourful and nutritious addition to a varied fruit intake.',
    storage_tips = 'Allow firm Papayas to ripen at room temperature. Once ripe, refrigerate to slow further softening. Cut Papaya should be placed in a covered food-safe container and refrigerated.'
WHERE name = 'boppaya (papaya)';

-- #32 Cabbage – Small Size → `Cabbage (small size)` (id 69)
UPDATE products
SET description = 'Small Size Cabbage offers the same crisp texture and mild flavour as larger cabbage but in a convenient compact size. It is ideal when controlled portions or smaller quantities are required. It can be used in curries, salads, stir-fries, fried rice, noodles, soups, rolls, and Indo-Chinese preparations.',
    benefits = 'Cabbage naturally provides dietary fibre, vitamin C, vitamin K, and plant nutrients. Its low calorie content and versatility make it easy to include in everyday vegetable preparations.',
    storage_tips = 'Keep whole Cabbage refrigerated in the vegetable compartment. Avoid washing before storage. Once cut, wrap securely or keep in a sealed food-safe container and refrigerate.'
WHERE name = 'Cabbage (small size)';

-- #33 Green Capsicum (Green Bell Pepper) → `Capcicum green` (id 77)
UPDATE products
SET description = 'Green Capsicum, also known as Green Bell Pepper, has a fresh, slightly crisp flavour and crunchy texture. It is commonly used in curries, pizzas, sandwiches, salads, noodles, fried rice, stir-fries, grilled dishes, and Indo-Chinese preparations.',
    benefits = 'Green Capsicum naturally contains vitamin C, dietary fibre, and various plant nutrients. It adds colour, crunch, flavour, and nutritional variety to both cooked and raw dishes.',
    storage_tips = 'Store Green Capsicum unwashed in the refrigerator vegetable drawer. Keep it dry and avoid excessive moisture. Once sliced, refrigerate the pieces in an airtight food-safe container and use promptly.'
WHERE name = 'Capcicum green';

-- #34 Red Capsicum (Red Bell Pepper) → `Capsicum red` (id 78)
UPDATE products
SET description = 'Red Capsicum, also known as Red Bell Pepper, is recognised for its bright red colour, crisp texture, and naturally sweeter flavour compared with green capsicum. It is extensively used in salads, pizzas, pasta, sandwiches, stir-fries, grilled dishes, sauces, and premium restaurant preparations.',
    benefits = 'Red Capsicum is naturally a good source of vitamin C and carotenoid pigments, while also providing dietary fibre and other nutrients. Its vibrant colour and sweet flavour make it useful for both nutritional and presentation purposes.',
    storage_tips = 'Store Red Capsicum dry and unwashed in the refrigerator vegetable compartment. Avoid storing damaged or cut peppers uncovered. Refrigerate cut portions in an airtight container and use them as soon as reasonably possible.'
WHERE name = 'Capsicum red';

COMMIT;

-- Matched: 34 / 34
