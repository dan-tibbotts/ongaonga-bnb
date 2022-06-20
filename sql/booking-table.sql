
-- Create booking table
DROP TABLE IF EXISTS booking;
CREATE TABLE IF NOT EXISTS booking (
  bookingID int UNSIGNED NOT NULL AUTO_INCREMENT,
  customerID int UNSIGNED NOT NULL,
  roomID int UNSIGNED NOT NULL,
  checkindate date NOT NULL,
  checkoutdate date NOT NULL,
  contactnumber varchar(14) NOT NULL,
  bookingextras varchar(250) DEFAULT NULL,
  roomreview varchar(250) DEFAULT NULL,
  PRIMARY KEY (bookingID),
  CONSTRAINT FK_customerID FOREIGN KEY (customerID) REFERENCES customer(customerID),
  CONSTRAINT FK_roomID FOREIGN KEY (roomID) REFERENCES room(roomID)
) AUTO_INCREMENT=1;

-- 14 Rooms
-- 19 customers
-- No reviews after 2022-06-26

-- Insert booking data
INSERT INTO booking 
(customerID, roomID, checkindate, checkoutdate, contactnumber, bookingextras, roomreview)
VALUES 
(1, 6, "2022-06-03", "2022-06-05", "(021) 123 4567", "SkyTV", "Beautiful home with lovely views"),
(2, 10, "2022-06-06", "2022-06-09", "(022) 813 6548", "", "Highly reccommend"),
(3, 7, "2022-06-14", "2022-06-15", "(025) 931 6746", "", "Time went too quickly, but I liked the place"),
(4, 9, "2022-06-22", "2022-06-30", "(027) 233 3670", "Please give me a TV guide", ""),
(5, 1, "2022-06-13", "2022-06-15", "(022) 567 1040", "", "I'll be back (with my dog)"),
(6, 8, "2022-06-08", "2022-06-09", "(020) 050 7951", "Vegetarian diet only please", "Room needs a paint job, other than that it was a lovely stay"),
(7, 11, "2022-06-13", "2022-06-15", "(027) 703 0761", "", "My bed was uncomfortable"),
(8, 13, "2022-06-26", "2022-06-30", "(029) 570 0891", "No breakfast please", "Cheap place to stay in Ongaonga"),
(9, 14, "2022-06-08", "2022-06-13", "(025) 951 3571", "Daily room service needed", "Had a good time"),
(10, 5, "2022-06-05", "2022-06-10", "(025) 852 4382", "", "Lovely couple, highly recommend. A++"),
(11, 2, "2022-06-23", "2022-06-25", "(029) 125 4954", "Toast for breakfast", "My toast was burnt"),
(12, 3, "2022-06-19", "2022-06-23", "(026) 901 0505", "SkyTV", "I really liked this place, thank you very much!"),
(13, 12, "2022-08-07", "2022-08-10", "(024) 954 6185", "", ""),
(14, 4, "2022-08-01", "2022-08-05", "(023) 951 9050", "Need extra plugs for devices", ""),
(15, 7, "2022-05-12", "2022-05-15", "(022) 321 9821", "", "An ensuite would have been nice, but it was a good place to stay nonetheless"),
(16, 10, "2022-08-03", "2022-08-04", "(020) 853 4507", "Organic shampoo only", ""),
(17, 6, "2022-07-18", "2022-07-22", "(020) 239 7032", "", ""),
(18, 9, "2022-07-14", "2022-07-17", "(025) 920 4056", "VCR player needed", ""),
(19, 12, "2022-08-01", "2022-08-05", "(027) 842 8202", "", ""),
(20, 1, "2022-08-18", "2022-08-25", "(021) 888 6567", "Internet required", ""),
(1, 13, "2022-07-24", "2022-07-25", "(021) 349 8485", "", ""),
(2, 8, "2022-07-08", "2022-07-12", "(022) 813 6548", "", ""),
(3, 5, "2022-08-19", "2022-08-20", "(025) 931 6746", "", ""),
(4, 4, "2022-07-13", "2022-07-15", "(027) 233 3670", "TV Guide", ""),
(5, 2, "2022-07-18", "2022-07-21", "(022) 567 1040", "Im bringing my dog", ""),
(6, 14, "2022-08-23", "2022-08-25", "(020) 050 7951", "Vegetrain meals only please", ""),
(7, 11, "2022-08-19", "2022-08-22", "(027) 703 0761", "Comfortable bed please", ""),
(8, 3, "2022-07-15", "2022-07-20", "(029) 570 0891", "Daily Cooked Breakfast", ""),
(9, 5, "2022-08-01", "2022-08-04", "(025) 951 3571", "Room service please", "")

