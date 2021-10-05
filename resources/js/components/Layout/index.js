import React from 'react';
import Drawer from "../Drawer";

const Index = (props) => {
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

export default Index;