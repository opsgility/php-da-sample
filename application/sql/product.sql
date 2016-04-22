
--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `name`, `url`, `created_date`, `updated_date`) VALUES
(1, 'Darkstar Grime Teal Complete Skateboard - 8" x 31"', 'darkstar-grime-teal-complete-skateboard-8x31', '2014-03-12 16:09:40', '2014-03-12 05:09:40');

-- --------------------------------------------------------


--
-- Dumping data for table `product_components`
--
INSERT INTO `product_components` (`id`, `component_type`, `component_details`, `product_id`, `created_date`, `updated_date`) VALUES
(1, 'Deck', 'Decks come in many different widths. If you are a beginner to skateboarding, choose your deck according to the width, not the length or wheelbase. The width you need depends on your size, skating style and personal preference. Here are some general guidelines.', 1, '0000-00-00 00:00:00', '2014-03-13 05:17:27'),
(2, 'Trucks', 'Skateboard trucks are the metal T-shaped pieces that mount onto the underside of the skateboard deck. When selecting skateboard trucks, the width of your truck axle should closely match the width of your skateboard deck. The truck size can be measured by the width of the hanger or the width of the axle. Every skateboard requires two trucks.\r\n\r\nThere are several parts that make up the skateboard trucks. The axle is the pin that runs through the trucks to which the wheels will attach. The hanger, usually made of metal, is the largest part of the skateboard truck that is somewhat triangular in shape. The axle runs through the hanger. The kingpin is the large bolt that holds these parts together and fits inside the skateboard bushings.\r\n\r\nWhen purchasing, you will receive the two trucks necessary to assemble your skateboard. Skateboard trucks come in various sizes and colors, and Warehouse Skateboards carries a huge selection of brands to fit your personal preference.', 1, '2014-03-13 16:17:39', '2014-03-13 05:17:39'),
(3, 'Wheels', 'Skateboard wheels vary in color, size and durability. Skateboard wheels are most commonly made of polyurethane. The diameter and durometer of the wheel affect the way the board rides. The diameter and durometer are a matter of personal preference and skating style.\r\n\r\nSkateboard Wheels at Warehouse Skateboards\r\n<b>Diameter</b> - All Skateboard wheels are measured in millimeters (mm). The smaller the number, the smaller the wheel. Smaller wheels are slower; bigger wheels are faster.', 1, '0000-00-00 00:00:00', '2014-03-13 09:48:36'),
(4, 'Personalize', 'Illustrates ways to personalize the board (graphics on the deck).', 1, '2014-03-13 20:49:10', '2014-03-13 09:49:10'),
(5, 'Specifications ', 'Weight 500gr <br />\r\nDimensions 30 X 80  <br />\r\nColors Red, Green, Blue', 1, '2014-03-14 12:58:55', '2014-03-14 01:58:55');
-- --------------------------------------------------------

--
-- Dumping data for table `product_media`
--

INSERT INTO `product_media` (`id`, `image_name`, `video_name`, `product_id`, `product_component_id`, `created_date`, `updated_date`) VALUES
(1, '1CDAR0GRIM800G7.jpg', '', 1, NULL, '2014-03-12 16:18:11', '2014-03-12 05:18:11'),
(2, 'wheel1.jpg', '', 1, 3, '2014-03-12 16:18:11', '2014-03-13 10:17:43'),
(3, 'truck1.jpg', '', 1, 2, '2014-03-12 16:18:11', '2014-03-13 10:17:43'),
(4, 'deck1.jpg,deck2.jpg', '', 1, 1, '2014-03-13 21:22:08', '2014-03-13 10:22:08'),
(5, 'personalize_graphic_1.jpg,personalize_graphic_2.jpg,personalize_graphic_3.jpg', '', 1, 4, '2014-03-14 12:55:40', '2014-03-14 01:55:40');
-- --------------------------------------------------------