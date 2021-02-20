import React from 'react';
import Drawer from "../components/Drawer";

const Layout = (props) => {
  return (
    <div className="wrapper">
      <Drawer/>

      <main className="main-content">
        <div className="main-content__navbar">
          Welcome, Username
        </div>
        {props.children}
      </main>
    </div>
  )
};

export default Layout;