DROP DATABASE IF EXISTS bnb;
CREATE DATABASE IF NOT EXISTS bnb;
USE bnb;

-- The rooms for the bed and breakfast
DROP TABLE IF EXISTS room;
CREATE TABLE IF NOT EXISTS room (
  roomID int unsigned NOT NULL auto_increment,
  roomname varchar(100) NOT NULL,
  description text default NULL,
  roomtype character default 'D',  
  beds int,
  PRIMARY KEY (roomID)
) AUTO_INCREMENT=1;
INSERT INTO `room` (`roomID`,`roomname`,`description`,`roomtype`,`beds`) VALUES (1,"Kellie","Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Curabitur sed tortor. Integer aliquam adipiscing","S",5);
INSERT INTO `room` (`roomID`,`roomname`,`description`,`roomtype`,`beds`) VALUES (2,"Herman","Lorem ipsum dolor sit amet, consectetuer","D",5);
INSERT INTO `room` (`roomID`,`roomname`,`description`,`roomtype`,`beds`) VALUES (3,"Scarlett","Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Curabitur","D",2);
INSERT INTO `room` (`roomID`,`roomname`,`description`,`roomtype`,`beds`) VALUES (4,"Jelani","Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Curabitur sed tortor. Integer aliquam","S",2);
INSERT INTO `room` (`roomID`,`roomname`,`description`,`roomtype`,`beds`) VALUES (5,"Sonya","Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Curabitur sed tortor. Integer aliquam adipiscing lacus.","S",5);
INSERT INTO `room` (`roomID`,`roomname`,`description`,`roomtype`,`beds`) VALUES (6,"Miranda","Lorem ipsum dolor sit amet, consectetuer adipiscing","S",4);
INSERT INTO `room` (`roomID`,`roomname`,`description`,`roomtype`,`beds`) VALUES (7,"Helen","Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Curabitur sed tortor. Integer aliquam adipiscing lacus.","S",2);
INSERT INTO `room` (`roomID`,`roomname`,`description`,`roomtype`,`beds`) VALUES (8,"Octavia","Lorem ipsum dolor sit amet,","D",3);
INSERT INTO `room` (`roomID`,`roomname`,`description`,`roomtype`,`beds`) VALUES (9,"Gretchen","Lorem ipsum dolor sit","D",3);
INSERT INTO `room` (`roomID`,`roomname`,`description`,`roomtype`,`beds`) VALUES (10,"Bernard","Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Curabitur sed tortor. Integer","S",5);
INSERT INTO `room` (`roomID`,`roomname`,`description`,`roomtype`,`beds`) VALUES (11,"Dacey","Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Curabitur","D",2);
INSERT INTO `room` (`roomID`,`roomname`,`description`,`roomtype`,`beds`) VALUES (12,"Preston","Lorem","D",2);
INSERT INTO `room` (`roomID`,`roomname`,`description`,`roomtype`,`beds`) VALUES (13,"Dane","Lorem ipsum dolor","S",4);
INSERT INTO `room` (`roomID`,`roomname`,`description`,`roomtype`,`beds`) VALUES (14,"Cole","Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Curabitur sed tortor. Integer aliquam","S",1);

-- Customers
DROP TABLE IF EXISTS customer;
CREATE TABLE IF NOT EXISTS customer (
  customerID int unsigned NOT NULL auto_increment,
  firstname varchar(50) NOT NULL,
  lastname varchar(50) NOT NULL,
  email varchar(100) NOT NULL,
  username varchar(25) NOT NULL,
  password varchar(40) NOT NULL,
  PRIMARY KEY (customerID)
) AUTO_INCREMENT=1;

INSERT INTO customer (customerID, firstname, lastname, email, username, password) 
VALUES 
(1,"Garrison","Jordan","sit.amet.ornare@nequesedsem.edu", "gjordan", "p@ssword1"),
(2,"Desiree","Collier","Maecenas@non.co.uk", "dcollier", "p@ssword1"),
(3,"Irene","Walker","id.erat.Etiam@id.org", "iwalker", "p@ssword1"),
(4,"Forrest","Baldwin","eget.nisi.dictum@a.com", "fbaldwin", "p@ssword1"),
(5,"Beverly","Sellers","ultricies.sem@pharetraQuisqueac.co.uk", "bsellers", "p@ssword1"),
(6,"Glenna","Kinney","dolor@orcilobortisaugue.org", "gkinney", "p@ssword1"),
(7,"Montana","Gallagher","sapien.cursus@ultriciesdignissimlacus.edu", "mgallagher", "p@ssword1"),
(8,"Harlan","Lara","Duis@aliquetodioEtiam.edu", "hlara", "p@ssword1"),
(9,"Benjamin","King","mollis@Nullainterdum.org", "bking", "p@ssword1"),
(10,"Rajah","Olsen","Vestibulum.ut.eros@nequevenenatislacus.ca", "rolsen", "p@ssword1"),
(11,"Castor","Kelly","Fusce.feugiat.Lorem@porta.co.uk", "ckelly", "p@ssword1"),
(12,"Omar","Oconnor","eu.turpis@auctorvelit.co.uk", "ooconnor", "p@ssword1"),
(13,"Porter","Leonard","dui.Fusce@accumsanlaoreet.net", "pleonard", "p@ssword1"),
(14,"Buckminster","Gaines","convallis.convallis.dolor@ligula.co.uk", "bgaines", "p@ssword1"),
(15,"Hunter","Rodriquez","ridiculus.mus.Donec@est.co.uk", "hrodriquez", "p@ssword1"),
(16,"Zahir","Harper","vel@estNunc.com", "zharper", "p@ssword1"),
(17,"Sopoline","Warner","vestibulum.nec.euismod@sitamet.co.uk", "swarner", "p@ssword1"),
(18,"Burton","Parrish","consequat.nec.mollis@nequenonquam.org", "bparrish", "p@ssword1"),
(19,"Abbot","Rose","non@et.ca", "arose", "p@ssword1"),
(20,"Barry","Burks","risus@libero.net", "bburks", "p@ssword1"),
(21,"Daniel","Tibbotts","dan.tibbotts@gmail.com", "dtibbotts", "GodIsGood7%"),
(22,"Jun","Han","junh@whitecliffe.ac.nz", "jhan1", "abcd1234!@#$");


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