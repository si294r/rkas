### Database: `rkas`

Table structure for table `rkas`
```
CREATE TABLE `rkas` (
  `rkas_id` varchar(50) NOT NULL,
  `kegiatan` varchar(100) DEFAULT NULL,
  `kode_rekening` varchar(100) DEFAULT NULL,
  `urutan` varchar(10) DEFAULT NULL,
  `uraian_kegiatan` varchar(200) DEFAULT NULL,
  `harga_satuan` int(11) DEFAULT NULL,
  `satuan_item` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

Indexes for table `rkas`
```
ALTER TABLE `rkas`
  ADD PRIMARY KEY (`rkas_id`);
```
