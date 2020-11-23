import React from 'react'
import { StyleSheet, Text, View, Image, TextInput, ScrollView, TouchableOpacity } from 'react-native'
import iconHome from './assets/icon/Home.png'
import iconTask from './assets/icon/Task.png'
import iconHelp from './assets/icon/Help.png'
import iconInbox from './assets/icon/Inbox.png'
import iconAccount from './assets/icon/Account.png'
import iconSearch from './assets/icon/Search.png'
import iconNew from './assets/icon/New.png'
import iconLoans from './assets/icon/Loans.png'
import iconCheck from './assets/icon/Check.png'
import iconUpload from './assets/icon/Upload.png'
import iconMore from './assets/icon/More.png'
import iconLib from './assets/icon/Lib.png'
import iconBuku from './assets/icon/Buku.png'
import iconSkripsi from './assets/icon/Skripsi.png'
import iconEbook from './assets/icon/Ebook.png'
import iconJournal from './assets/icon/Journal.png'
import iconRepo from './assets/icon/Repositori.png'
import iconAgenda from './assets/icon/Agenda.png'
import iconVideo from './assets/icon/Video.png'
import iconRrelasi from './assets/icon/Relasi.png'
import imageSeminar from './assets/image/Seminar.jpeg'
import imageBanner from './assets/image/Banner.jpg'
import imageOspek from './assets/image/Ospek.jpeg'
import imageKonek from './assets/image/Konek.png'
import iconUnida from './assets/icon/UnidaPutih.png'
import iconLibPutih from './assets/icon/LibPutih.png'
import imageNewBook1 from './assets/image/NewBook1.jpeg'

const App = () => {
  return (
    <View 
      style={{
        flex: 1}}>
      <ScrollView 
        style={{
          flex: 1, 
          backgroundColor: '#0984e3',}}>

            {/* Search Bar */}
            <View style={{marginHorizontal: 17, flexDirection: 'row', paddingTop: 15}}>
              <View style={{position: 'relative', flex: 1}}>
                <TextInput placeholder="What do you want to read?" style={{borderWidth: 1, borderColor: '#E8E8E8', borderRadius: 25, height: 40, fontSize: 13, paddingLeft: 45, paddingRight: 22, backgroundColor: 'white', marginRight: 18}}/>
                <Image source={iconSearch} style={{position: 'absolute', top: 8, left: 13, height:10, width: 26, height: 26}}/>
              </View>
              <View style={{width: 35}}>
                <Image source={iconNew} style={{width: 42, height: 35, top: 2, right: 5}}/>
              </View>
            </View>
            {/* Circulation */}
            <View style={{marginHorizontal: 17, marginTop: 20}}>
              <View style={{backgroundColor: 'white',flexDirection: 'row', justifyContent: 'space-between', borderTopLeftRadius: 8, borderTopRightRadius: 8, padding: 14}}>
                <Image source={iconLib} style={{width: 80, height: 35}} />
                <View style={{flexDirection: 'column'}}>
                  <Text style={{fontSize: 14, fontWeight: 'bold', color: '#0984e3', top: -5}}>M. TAUFIQ RIZA</Text>
                  <Text style={{fontSize: 14, fontWeight: 'bold', color: '#0984e3', top: -5}}>ID.402019611021</Text>
                </View>
              </View>
              <View style={{flexDirection: 'row', paddingTop: 20, paddingBottom: 14, backgroundColor: 'white', borderBottomRightRadius:2, borderBottomLeftRadius: 2}}>
                <View style={{flex: 1, alignItems:'center'}}>
                  <Image source={iconLoans} style={{width: 46, height: 45}}/>
                  <Text style={{fontSize: 13, fontWeight: 'bold', color: '#2A7FFF', marginTop: 9}}>Pinjam</Text>
                </View>
                <View style={{flex: 1, alignItems:'center'}}>
                  <Image source={iconCheck} style={{width: 47, height: 45}}/>
                  <Text style={{fontSize: 13, fontWeight: 'bold', color: '#2A7FFF', marginTop: 9}}>Check</Text>
                </View>
                <View style={{flex: 1, alignItems:'center'}}>
                  <Image source={iconUpload} style={{width: 48, height: 45}}/>
                  <Text style={{fontSize: 13, fontWeight: 'bold', color: '#2A7FFF', marginTop: 9}}>Upload</Text>
                </View>
                <View style={{flex: 1, alignItems:'center'}}>
                  <Image source={iconMore} style={{width: 47, height: 45}}/>
                  <Text style={{fontSize: 13, fontWeight: 'bold', color: '#2A7FFF', marginTop: 9}}>Lainnya</Text>
                </View>
              </View>
            </View>
            {/* Mian Fiture */}
            <View style={{flexDirection: 'row', flexWrap: 'wrap', marginTop: 18}}>
              <View style={{justifyContent: 'space-between', flexDirection: 'row', width: '100%', marginBottom: 18}}>
                <View style={{width: '25%', alignItems: 'center'}}>
                  <View style={{width:58, height: 58, justifyContent: 'center', alignItems: 'center'}}>
                    <Image source={iconBuku} style={{width: 50, height: 50}}/>
                  </View>
                    <Text style={{fontSize: 11, fontWeight: 'bold', textAlign: 'center', marginTop: 6, color: 'white'}}>Buku</Text>
                </View>
                
                <View style={{width: '25%', alignItems: 'center'}}>
                  <View style={{width:58, height: 58, justifyContent: 'center', alignItems: 'center'}}>
                    <Image source={iconSkripsi} style={{width: 50, height: 50}}/>
                  </View>
                    <Text style={{fontSize: 11, fontWeight: 'bold', textAlign: 'center', marginTop: 6, color: 'white'}}>Skripsi</Text>
                </View>
                
                <View style={{width: '25%', alignItems: 'center'}}>
                  <View style={{width:58, height: 58, justifyContent: 'center', alignItems: 'center'}}>
                    <Image source={iconEbook} style={{width: 50, height: 50}}/>
                  </View>
                    <Text style={{fontSize: 11, fontWeight: 'bold', textAlign: 'center', marginTop: 6, color: 'white'}}>E-Book</Text>
                </View>
                
                <View style={{width: '25%', alignItems: 'center'}}>
                  <View style={{width:58, height: 58, justifyContent: 'center', alignItems: 'center'}}>
                    <Image source={iconJournal} style={{width: 50, height: 50}}/>
                  </View>
                    <Text style={{fontSize: 11, fontWeight: 'bold', textAlign: 'center', marginTop: 6, color: 'white'}}>Journal</Text>
                </View>


              </View>
              <View style={{justifyContent: 'space-between', flexDirection: 'row', width: '100%', marginBottom: 18}}>
                <View style={{width: '25%', alignItems: 'center'}}>
                  <View style={{width:58, height: 58, justifyContent: 'center', alignItems: 'center'}}>
                    <Image source={iconRepo} style={{width: 50, height: 50}}/>
                  </View>
                  <Text style={{fontSize: 11, fontWeight: 'bold', textAlign: 'center', marginTop: 6, color: 'white'}}>Repositori</Text>
                </View>
                
                <View style={{width: '25%', alignItems: 'center'}}>
                  <View style={{width:58, height: 58, justifyContent: 'center', alignItems: 'center'}}>
                    <Image source={iconAgenda} style={{width: 50, height: 50}}/>
                  </View>
                    <Text style={{fontSize: 11, fontWeight: 'bold', textAlign: 'center', marginTop: 6, color: 'white'}}>Agenda</Text>
                </View>
                
                <View style={{width: '25%', alignItems: 'center'}}>
                  <View style={{width:58, height: 58, justifyContent: 'center', alignItems: 'center'}}>
                    <Image source={iconVideo} style={{width: 50, height: 50}}/>
                  </View>
                    <Text style={{fontSize: 11, fontWeight: 'bold', textAlign: 'center', marginTop: 6, color: 'white'}}>Video</Text>
                </View>
                
                <View style={{width: '25%', alignItems: 'center'}}>
                  <View style={{width:58, height: 58, justifyContent: 'center', alignItems: 'center'}}>
                    <Image source={iconRrelasi} style={{width: 50, height: 50}}/>
                  </View> 
                    <Text style={{fontSize: 11, fontWeight: 'bold', textAlign: 'center', marginTop: 6, color: 'white'}}>Relaasi</Text>
                </View>
              </View>
            </View>
            {/* Internal Information */}
            <View style={{height: 3, backgroundColor: 'white', marginHorizontal: 17, borderRadius: 8}}></View>
            <View style={{padding: 17, paddingBottom: 0,}}>
              <View style={{width: 120, height: 75, marginLeft: 140, flexDirection: 'row', marginTop: -25, marginBottom: -25}}>
                <Image source={iconUnida} style={{width: undefined, height: undefined, resizeMode: 'contain', flex: 1}}/>
                <Image source={iconLibPutih} style={{width: undefined, height: undefined, resizeMode: 'contain', flex: 1}}/>
              </View>
                <Text style={{fontSize: 16, fontWeight: 'bold', color: 'white', marginTop: 15, marginBottom: 15}}>Lengkapi Profilmu</Text>
            <View style={{flexDirection: 'row', marginBottom: 16}}>
                <View>
                  <Image source={imageKonek} style={{width: 150, height: 50}}/>
                </View>
                <View style={{marginLeft: 16, flex: 1}}>
                  <Text style={{fontSize: 13, fontWeight: 'bold', color: 'white'}}>Sambungkan dengan SIAKAD UNIDA</Text>
                  <Text style={{fontSize: 13, fontWeight: 'normal', color: 'white', width: '70%'}}>Masuk lebih cepat tanpa menggunakan kode verifikasi</Text>
                </View>
              </View>
                <View style={{flex: 1, paddingLeft: 260}}>
                  <TouchableOpacity style={{backgroundColor: '#b3b3b3ff', paddingHorizontal: 12, paddingVertical: 11, alignSelf: 'stretch', borderRadius: 5}}>
                    <Text style={{fontSize: 13, fontWeight: 'bold', color: 'white', textAlign: 'center'}}>SAMBUNGKAN</Text>
                  </TouchableOpacity>
                </View>
              </View>
            {/* Best Notif */}
            <View style={{height: 3, backgroundColor: 'white', marginHorizontal: 17, borderRadius: 8, marginTop: 10}}></View>
            <View style={{padding:16}}>
              <View style={{position: 'relative'}}>
                <Image source={imageBanner} style={{height: 170, width: '100%', borderRadius: 8}}/>
                <View style={{width: '100%', height: '100%', position: 'absolute', backgroundColor: 'black', opacity: 0.15, borderRadius: 6}}></View>
                <View style={{width: 70, height: 25, position: 'absolute', top: 16, left: 16}}>
                    <Image source={iconLib} style={{width: undefined, height: undefined, resizeMode: 'contain', flex: 1}}/>
                </View>
                <View style={{position: 'absolute', bottom: 0, left: 0, width: '100%', flexDirection: 'row', alignItems: 'center', paddingHorizontal: 16, paddingBottom: 16}}>
                  <View>
                    <Text style={{fontSize: 18, fontWeight: 'bold', color: 'white', marginBottom: 18}}>Gratis Voucher E-Book </Text>
                    <Text style={{fontSize: 12, fontWeight: '400', color: 'white'}}>Dapatkan sebelum habis!</Text>
                  </View>
                  <View style={{flex: 1, paddingLeft: 12}}>
                  <TouchableOpacity style={{backgroundColor: '#b3b3b3ff', paddingHorizontal: 12, paddingVertical: 11, alignSelf: 'stretch', borderRadius: 5}}>
                    <Text style={{fontSize: 13, fontWeight: 'bold', color: 'white', textAlign: 'center'}}>DAPATKAN VOCHER</Text>
                  </TouchableOpacity>
                  </View>
                </View>
              </View>
            </View>
            {/* Lib News 2 */}
            <View style={{height: 3, backgroundColor: 'white', marginTop: 20, marginHorizontal: 17, borderRadius: 8}}></View>
            <View style={{paddingTop: 16, paddingHorizontal: 16}}>
              <View style={{position: 'relative'}}>
                  <Image source={imageOspek} style={{height: 170, width: '100%', borderRadius: 8}}/>
                  <View style={{width: 70, height: 25, position: 'absolute', top: 16, left: 16}}>
                    <Image source={iconLib} style={{width: undefined, height: undefined, resizeMode: 'contain', flex: 1}}/>
                  </View>
              </View>
              <View style={{paddingTop: 16, paddingBottom: 20, borderBottomColor: 'white'}}>
                <Text style={{fontSize: 16, fontWeight: 'bold', color: 'white'}}>U-LIB NEWS</Text>
                <Text style={{fontSize: 14, color: 'white', marginBottom: 11}}>Pengenalan Perpustakaan Unida gontor, kepada mahasiswa baru. Pada acara Ospek 2020</Text>
                <TouchableOpacity style={{backgroundColor: '#b3b3b3ff', paddingHorizontal: 12, paddingVertical: 11, alignSelf: 'flex-end', borderRadius: 5}}>
                  <Text style={{fontSize: 13, fontWeight: 'bold', color: 'white', textAlign: 'center'}}>BACA</Text>
                </TouchableOpacity>
              </View>
            </View>
            {/* News Book World */}
            <View style={{height: 3, backgroundColor: 'white', marginTop: 0,marginBottom: 9, marginHorizontal: 17, borderRadius: 8}}></View>
            <View>
              <View style={{width: 90, height: 45, marginLeft: -6, marginLeft: 17}}>
                <Image source={iconLibPutih} style={{width: undefined, height: undefined, resizeMode: 'contain', flex: 1}}/>
              </View>
            </View>
            <View>
              <View style={{flexDirection: 'row', justifyContent: 'space-between', marginBottom: 15, marginHorizontal: 17}}>
                <Text style={{fontSize: 16, fontWeight: 'bold', color: 'white',}}>Koleksi Buku Dunia Terbaru</Text>
                <Text style={{fontSize: 16, fontWeight: 'bold', color: '#b3b3b3ff'}}>Lihat Semua</Text>
              </View>
              <ScrollView horizontal style={{flexDirection: 'row', paddingLeft: 17, paddingRight: 17}}>
                <View style={{marginRight: 15}}>
                  <View style={{width: 150, height: 150,}}>
                    <Image source={imageNewBook1} style={{width: undefined, height: undefined, resizeMode: 'cover', flex: 1, borderRadius: 10}}/>
                  </View>
                  <Text style={{fontSize: 15, fontWeight: 'bold', color: 'white', marginTop: 10}}>Buku Baru</Text>
                </View>
                <View style={{marginRight: 15}}>
                  <View style={{width: 150, height: 150,}}>
                    <Image source={imageNewBook1} style={{width: undefined, height: undefined, resizeMode: 'cover', flex: 1, borderRadius: 10}}/>
                  </View>
                  <Text style={{fontSize: 15, fontWeight: 'bold', color: 'white', marginTop: 10}}>Buku Baru</Text>
                </View>
                <View style={{marginRight: 15}}>
                  <View style={{width: 150, height: 150,}}>
                    <Image source={imageNewBook1} style={{width: undefined, height: undefined, resizeMode: 'cover', flex: 1, borderRadius: 10}}/>
                  </View>
                  <Text style={{fontSize: 15, fontWeight: 'bold', color: 'white', marginTop: 10}}>Buku Baru</Text>
                </View>
                <View style={{marginRight: 15}}>
                  <View style={{width: 150, height: 150,}}>
                    <Image source={imageNewBook1} style={{width: undefined, height: undefined, resizeMode: 'cover', flex: 1, borderRadius: 10}}/>
                  </View>
                  <Text style={{fontSize: 15, fontWeight: 'bold', color: 'white', marginTop: 10}}>Buku Baru</Text>
                </View>
                <View style={{marginRight: 15}}>
                  <View style={{width: 150, height: 150,}}>
                    <Image source={imageNewBook1} style={{width: undefined, height: undefined, resizeMode: 'cover', flex: 1, borderRadius: 10}}/>
                  </View>
                  <Text style={{fontSize: 15, fontWeight: 'bold', color: 'white', marginTop: 10}}>Buku Baru</Text>
                </View>
                <View style={{marginRight: 15}}>
                  <View style={{width: 150, height: 150,}}>
                    <Image source={imageNewBook1} style={{width: undefined, height: undefined, resizeMode: 'cover', flex: 1, borderRadius: 10}}/>
                  </View>
                  <Text style={{fontSize: 15, fontWeight: 'bold', color: 'white', marginTop: 10}}>Buku Baru</Text>
                </View>
              </ScrollView>
            </View>
            {/* Lib News 1 */}
            <View style={{height: 3, backgroundColor: 'white', marginTop: 20, marginHorizontal: 17, borderRadius: 8}}></View>
            <View style={{paddingTop: 16, paddingHorizontal: 16}}>
              <View style={{position: 'relative'}}>
                  <Image source={imageSeminar} style={{height: 170, width: '100%', borderRadius: 8}}/>
                  <View style={{width: 70, height: 25, position: 'absolute', top: 16, left: 16}}>
                    <Image source={iconLib} style={{width: undefined, height: undefined, resizeMode: 'contain', flex: 1}}/>
                  </View>
              </View>
              <View style={{paddingTop: 16, paddingBottom: 20, borderBottomColor: 'white'}}>
                <Text style={{fontSize: 16, fontWeight: 'bold', color: 'white'}}>U-LIB NEWS</Text>
                <Text style={{fontSize: 14, color: 'white', marginBottom: 11}}>Perpustakaan Unida gontor Mengadakan Seminar tentang keperpustakaan, yang diakadakan di kampus mantingan, yang dihadiri langsung oleh direktue perpustakaan Unida Pusat</Text>
                <TouchableOpacity style={{backgroundColor: '#b3b3b3ff', paddingHorizontal: 12, paddingVertical: 11, alignSelf: 'flex-end', borderRadius: 5}}>
                  <Text style={{fontSize: 13, fontWeight: 'bold', color: 'white', textAlign: 'center'}}>BACA</Text>
                </TouchableOpacity>
              </View>
            </View>    
      </ScrollView>
      {/* Bar Navigation */}
      <View
        style={{
          height: 56, 
          flexDirection: 'row',
          backgroundColor: 'white'}}>
        <View 
          style={{
            flex: 1,
            alignItems: 'center',
            justifyContent: 'center'}}>
            <Image 
              source={iconHome} 
              style={{width:26, height: 26}}/>
          <Text 
            style={{
              fontSize: 10, 
              color: '#545454', 
              marginTop: 4}}>
                Home
          </Text>
        </View>
        <View 
          style={{
            flex: 1,
            alignItems: 'center',
            justifyContent: 'center'}}>
            <Image 
              source={iconTask} 
              style={{width:26, height: 26}}/>
        <Text 
          style={{
            fontSize: 10, 
            color: '#545454', 
            marginTop: 4}}>
              Loans
        </Text>
        </View>
        <View 
          style={{ 
            flex: 1,
            alignItems: 'center',
            justifyContent: 'center'}}>
            <Image 
              source={iconHelp} 
              style={{width:25, height: 27}}/>
        <Text 
          style={{
            fontSize: 10, 
            color: '#545454', 
            marginTop: 4}}>
              Help
              </Text>
        </View>
        <View 
          style={{
            flex: 1,
            alignItems: 'center',
            justifyContent: 'center'}}>
            <Image 
              source={iconInbox} 
              style={{width:26, height: 26}}/>
        <Text 
          style={{
            fontSize: 10, 
            color: '#545454', 
            marginTop: 4}}>
              Inbox
              </Text>
        </View>
        <View 
          style={{
            flex: 1,
            alignItems: 'center',
            justifyContent: 'center'}}>
            <Image 
              source={iconAccount} 
              style={{width:24, height: 26}}/>
        <Text 
          style={{
            fontSize: 10, 
            color: '#545454', 
            marginTop: 4}}>Account</Text>
        </View>
      </View>
    </View>
  )
}

export default App

const styles = StyleSheet.create({})