import React from 'react';
import Drawer from "../Drawer";

const Index = (props) => {
  const panel = window.panel;

  return (
    <div className="wrapper">
      <Drawer/>

      <main className="main-content">
        <div className="main-content__navbar">
          Welcome, { panel.auth ? panel.auth.name : 'User' }
        </div>
        {props.children}
      </main>
    </div>
  )
};

export default Index;