INSERT INTO kelas (id_kelas, nama_kelas, jurusan)
VALUES
    (1, 'XI PPLG', 'PPLG'),
    (2, 'XI TJKT', 'TJKT'),
    (3, 'XI MPLB', 'MPLB'),
    (4, 'XI DKV', 'DKV');

INSERT INTO siswa (nisn, nama, id_kelas)
VALUES
    ('1234567890', 'Radhitya Annora Putra', 1),
    ('0987654321', 'Muhammad Bayu Romadhani', 2),
    ('2345678901', 'Khalifa Ferry Irwandi', 3),
    ('3456789012', 'Radoslaw Ngo', 4);

INSERT INTO users (username, password)
VALUES
    ('admin', 'syabilmiripkeonho');
-- Buat akun administrator melalui PHP password_hash(), jangan simpan password biasa.
